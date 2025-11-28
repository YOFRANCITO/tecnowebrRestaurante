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
        Schema::create('pagos_facil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credito_id')->constrained('creditos')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('pagofacil_transaction_id')->nullable(); // ID PagoFácil
            $table->string('email')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 10)->default('BOB');
            $table->string('estado')->nullable(); // pending, paid, failed, expired
            $table->string('descripcion')->nullable();
            $table->foreignId('usuario_id')->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_facil');
    }
};
