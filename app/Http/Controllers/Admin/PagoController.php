<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Credito;
use App\Models\PageVisit;
use App\Models\PagoFacil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource (mis pagos).
     */
    public function index(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.pagos.index');

        $query = Pago::with('credito.venta.user', 'credito.user');

        if (auth()->user()->hasRole('cliente')) {
            $query->whereHas('credito', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $pagos = $query->orderBy('fecha', 'desc')->paginate(10);

        return Inertia::render('Restaurant/Pagos/Index', [
            'pagos' => $pagos,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Formulario de creación
     */
    public function create(Request $request)
    {
        $visitCount = PageVisit::incrementVisit('restaurant.pagos.create');

        $creditos = Credito::with('venta')
            ->where('user_id', auth()->id())
            ->pendientes()
            ->get();

        $creditoSeleccionado = null;

        if ($request->filled('credito_id')) {
            $creditoSeleccionado = Credito::with(['venta', 'plan'])
                ->where('user_id', auth()->id())
                ->findOrFail($request->credito_id);

            $creditoSeleccionado->intereses_acumulados =
                $creditoSeleccionado->calcularInteresesDiarios();
        }

        return Inertia::render('Restaurant/Pagos/Create', [
            'creditos' => $creditos,
            'creditoSeleccionado' => $creditoSeleccionado,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * STORE normal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'monto' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $credito = Credito::where('user_id', auth()->id())
                ->findOrFail($validated['credito_id']);

            if ($credito->saldo_final <= 0) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Este crédito ya está pagado.']);
            }

            $interesesAcumulados = $credito->calcularInteresesDiarios();
            $credito->interes += $interesesAcumulados;

            $monto = $validated['monto'];
            $pagoIntereses = min($monto, $credito->interes);
            $pagoCapital = $monto - $pagoIntereses;

            $credito->interes -= $pagoIntereses;
            $credito->capital -= $pagoCapital;
            $credito->actualizarSaldo();

            Pago::create([
                'credito_id' => $credito->id,
                'monto'      => $monto,
                'fecha'      => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('restaurant.pagos.index')
                ->with('success', 'Pago registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }

    // ================================
    // 🔵 TOKEN PAGO FÁCIL
    // ================================
    private function obtenerTokenPagoFacil()
    {
        $service = "51247fae280c20410824977b0781453df59fad5b23bf2a0d14e884482f91e09078dbe5966e0b970ba696ec4caf9aa5661802935f86717c481f1670e63f35d504a62547a9de71bfc76be2c2ae01039ebcb0f74a96f0f1f56542c8b51ef7a2a6da9ea16f23e52ecc4485b69640297a5ec6a701498d2f0e1b4e7f4b7803bf5c2eba";
        $secret  = "0C351C6679844041AA31AF9C";

        $client = new Client();

        $response = $client->post("https://masterqr.pagofacil.com.bo/api/services/v2/login", [
            'headers' => [
                'tcTokenService' => $service,
                'tcTokenSecret'  => $secret,
                'Accept'         => 'application/json',
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data["values"]["accessToken"])) {
            throw new \Exception("No se recibió accessToken. Respuesta: " . json_encode($data));
        }

        return $data["values"]["accessToken"];
    }

    // ================================
    // 🔵 GENERAR QR
    // ================================
    public function pagoqr(Request $request)
    {
        Log::info("📥 [pagoqr] Request recibido", $request->all());

        try {
            // -------------------------------
            // VALIDAR DATOS RECIBIDOS
            // -------------------------------
            $request->validate([
                'credito_id' => 'required|exists:creditos,id',
                'monto'      => 'required|numeric|min:0.01',
            ]);

            Log::info("📌 [pagoqr] Datos validados correctamente", [
                'credito_id' => $request->credito_id,
                'monto'      => $request->monto,
            ]);

            $credito = Credito::where('user_id', auth()->id())
                ->findOrFail($request->credito_id);

            Log::info("📌 [pagoqr] Crédito encontrado", [
                'credito_id'   => $credito->id,
                'credito_nro'  => $credito->nro,
                'saldo_final'  => $credito->saldo_final,
            ]);

            $monto = (float) $request->monto;

            // -------------------------------
            // TOKEN
            // -------------------------------
            $token = $this->obtenerTokenPagoFacil();
            Log::info("🔑 [pagoqr] Token obtenido correctamente");

            $client = new Client();
            $paymentNumber = "PF-" . rand(100000, 999999);

            $headers = [
                "Content-Type"  => "application/json",
                "Authorization" => "Bearer " . $token
            ];

            $body = [
                "paymentMethod" => 4,
                "clientName"    => auth()->user()->name,
                "documentType"  => 1,
                "documentId"    => "77635162",
                "phoneNumber"   => "77635162",
                "email"         => auth()->user()->email,
                "paymentNumber" => $paymentNumber,
                "amount"        => $monto,
                "currency"      => 2,
                "clientCode"    => "CRED-" . $credito->id,
                "callbackUrl"   => "https://casoespecial.superficct.com/api/pago/callback",
                "orderDetail" => [
                    [
                        "serial"    => 1,
                        "product"   => "Pago crédito #" . $credito->nro,
                        "quantity"  => 1,
                        "price"     => $monto,
                        "discount"  => 0,
                        "total"     => $monto
                    ]
                ]
            ];

            Log::info("📤 [pagoqr] Request enviado a PagoFácil", [
                'url'     => "https://masterqr.pagofacil.com.bo/api/services/v2/generate-qr",
                'headers' => $headers,
                'body'    => $body,
            ]);

            // -------------------------------
            // GENERAR QR
            // -------------------------------

            $response = $client->post(
                "https://masterqr.pagofacil.com.bo/api/services/v2/generate-qr",
                ["headers" => $headers, "json" => $body]
            );

            $result = json_decode($response->getBody()->getContents(), true);

            Log::info("📥 [pagoqr] Respuesta recibida de PagoFácil", [
                'result' => $result,
            ]);

            if (!isset($result["values"]["qrBase64"])) {
                Log::error("❌ [pagoqr] PagoFácil no devolvió QR", ['result' => $result]);
                dd("Error generando QR:", $result);
            }

            // -------------------------------
            // REGISTRAR COMO PENDIENTE
            // -------------------------------
            PagoFacil::create([
                'credito_id' => $credito->id, 
                'pagofacil_transaction_id' => $result["values"]["transactionId"],
                'email'        => auth()->user()->email,
                'monto'        => $monto,
                'moneda'       => 'BOB',
                'estado'       => 'pendiente',
                'descripcion'  => 'Pago QR de crédito',
                'usuario_id'   => auth()->id(),
            ]);

            Log::info("💾 [pagoqr] Pago registrado como pendiente", [
                'transaccion' => $result["values"]["transactionId"],
                'monto'       => $monto
            ]);

            // -------------------------------
            // ENVIAR A LA VISTA QR
            // -------------------------------
            return Inertia::render('Restaurant/Pagos/QR', [
                'lnNroTran' => $result["values"]["transactionId"],
                'laQrImage' => "data:image/png;base64," . $result["values"]["qrBase64"],
                'credito_id' => $request->credito_id, // 👈 NECESARIO
            ]);
        } catch (\Throwable $e) {

            Log::error("🔥 [pagoqr] ERROR", [
                'mensaje' => $e->getMessage(),
                'linea'   => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function procesarPagoCredito($credito_id, $monto)
    {
        DB::beginTransaction();

        try {
            $credito = Credito::where('user_id', auth()->id())
                ->findOrFail($credito_id);

            if ($credito->saldo_final <= 0) {
                DB::rollBack();
                throw new \Exception("El crédito ya está pagado.");
            }

            // Calcular intereses
            $interesesAcumulados = $credito->calcularInteresesDiarios();
            $credito->interes += $interesesAcumulados;

            $pagoIntereses = min($monto, $credito->interes);
            $pagoCapital = $monto - $pagoIntereses;

            $credito->interes -= $pagoIntereses;
            $credito->capital -= $pagoCapital;
            $credito->actualizarSaldo();

            // Registrar pago real
            Pago::create([
                'credito_id' => $credito->id,
                'monto'      => $monto,
                'fecha'      => Carbon::now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("❌ Error procesando pago QR", [
                'credito_id' => $credito_id,
                'monto'      => $monto,
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }


    // ================================
    // 🔵 CONSULTAR ESTADO PAGO FÁCIL
    // ================================
    public function ConsultarEstado(Request $request)
    {
        try {
            $token = $this->obtenerTokenPagoFacil();
            $client = new Client();

            $response = $client->post(
                "https://masterqr.pagofacil.com.bo/api/services/v2/query-transaction",
                [
                    'headers' => [
                        "Content-Type"  => "application/json",
                        "Authorization" => "Bearer " . $token
                    ],
                    'json' => [
                        "pagofacilTransactionId" => $request->pagofacilTransactionId
                    ]
                ]
            );

            $data = json_decode($response->getBody(), true);

            return response()->json([
                "error" => false,
                "values" => $data["values"]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => true,
                "message" => $e->getMessage()
            ]);
        }
    }

    // ================================
    // 🔵 MARCAR PAGO COMO COMPLETADO
    // ================================

    public function crearpago(Request $request)
    {
        Log::info("🟢 [crearpago] Registrando pago FINAL", $request->all());

        $pf = PagoFacil::where('pagofacil_transaction_id', $request->transaccion)->first();

        if (!$pf) {
            return response()->json(['error' => true, 'message' => 'Pago no encontrado']);
        }

        // Marcar como pagado
        $pf->estado = 'pagado';
        $pf->save();

        // 👇 Ahora sí mandamos el crédito correcto
        $this->procesarPagoCredito(
            $pf->credito_id ?? $request->credito_id,  // fallback si no está en base
            $pf->monto
        );

        return response()->json(['success' => true]);
    }





    public function success(Request $request)
    {
        $pago = PagoFacil::where('pagofacil_transaction_id', $request->transaccion)->first();

        return Inertia::render('Restaurant/Pagos/Success', [
            'transaccion' => $request->transaccion,
            'monto'       => $pago->monto ?? 0,
        ]);
    }
}
