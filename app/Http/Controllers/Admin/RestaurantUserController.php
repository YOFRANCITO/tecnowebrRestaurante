<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Cloudinary\Cloudinary;

class RestaurantUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Incrementar contador de visitas
        $visitCount = PageVisit::incrementVisit('restaurant.users.index');

        $query = User::with('roles');

        // Filtro por rol
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);
        $roles = Role::whereIn('name', ['administrador', 'almacenero', 'mesero', 'cliente'])->get();

        return Inertia::render('Restaurant/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['role', 'search']),
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $visitCount = PageVisit::incrementVisit('restaurant.users.create');
        $roles = Role::whereIn('name', ['administrador', 'almacenero', 'mesero', 'cliente'])->get();

        return Inertia::render('Restaurant/Users/Create', [
            'roles' => $roles,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['administrador', 'almacenero', 'mesero', 'cliente'])],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'foto.max' => 'La foto no puede ser mayor a 2MB.',
        ]);

        $profilePhotoPath = null;
        
        // Subir foto a Cloudinary si existe
        if ($request->hasFile('foto')) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ],
                ]);

                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $request->file('foto')->getRealPath(),
                    [
                        'folder' => 'usuarios',
                        'transformation' => [
                            'width' => 400,
                            'height' => 400,
                            'crop' => 'fill',
                            'gravity' => 'face',
                            'quality' => 'auto',
                        ],
                    ]
                );

                $profilePhotoPath = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['foto' => 'Error al subir la foto: ' . $e->getMessage()]);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_photo_path' => $profilePhotoPath,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('restaurant.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $visitCount = PageVisit::incrementVisit('restaurant.users.show');

        return Inertia::render('Restaurant/Users/Show', [
            'user' => $user,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $visitCount = PageVisit::incrementVisit('restaurant.users.edit');
        $roles = Role::whereIn('name', ['administrador', 'almacenero', 'mesero', 'cliente'])->get();

        return Inertia::render('Restaurant/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'visitCount' => $visitCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['administrador', 'almacenero', 'mesero', 'cliente'])],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'foto.max' => 'La foto no puede ser mayor a 2MB.',
        ]);

        // Subir nueva foto si existe
        if ($request->hasFile('foto')) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ],
                ]);

                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $request->file('foto')->getRealPath(),
                    [
                        'folder' => 'usuarios',
                        'transformation' => [
                            'width' => 400,
                            'height' => 400,
                            'crop' => 'fill',
                            'gravity' => 'face',
                            'quality' => 'auto',
                        ],
                    ]
                );

                $validated['profile_photo_path'] = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['foto' => 'Error al subir la foto: ' . $e->getMessage()]);
            }
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Actualizar foto si se subió una nueva
        if (isset($validated['profile_photo_path'])) {
            $user->update(['profile_photo_path' => $validated['profile_photo_path']]);
        }

        // Actualizar contraseña solo si se proporcionó
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Actualizar rol
        $user->syncRoles([$validated['role']]);

        return redirect()->route('restaurant.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     * Nota: Eliminación lógica (soft delete)
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // No permitir que el usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('restaurant.users.index')
            ->with('success', 'Usuario archivado exitosamente.');
    }
}

