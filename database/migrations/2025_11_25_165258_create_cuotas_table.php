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
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credito_id')->constrained('creditos')->onDelete('cascade');
            $table->integer('nro'); // Número de cuota
            $table->date('fecha'); // Fecha de vencimiento sugerida
            $table->decimal('saldo_inicial', 10, 2);
            $table->decimal('interes', 10, 2);
            $table->decimal('capital', 10, 2);
            $table->decimal('cuota', 10, 2);
            $table->decimal('saldo_final', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
