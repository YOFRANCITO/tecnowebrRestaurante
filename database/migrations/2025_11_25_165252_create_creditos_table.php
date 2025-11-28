<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->string('nro')->unique(); // Auto-generado: CRE-2025-00001
            $table->date('fecha');
            $table->decimal('saldo_inicial', 10, 2);
            $table->decimal('interes', 10, 2)->default(0);
            $table->decimal('capital', 10, 2);
            $table->decimal('cuota', 10, 2);
            $table->decimal('saldo_final', 10, 2);
            $table->foreignId('venta_id')->unique()->constrained('ventas')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('planes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};
