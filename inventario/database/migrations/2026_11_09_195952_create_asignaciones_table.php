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
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->restrictOnDelete();
            $table->enum('tipo', ['temporal', 'permanente'])->default('temporal');
            $table->date('fecha_recogida');
            $table->date('fecha_entrega')->nullable();   // vacía = lo tiene todavía
            $table->foreignId('sede_devolucion_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // quién lo registró
            $table->timestamps();

            $table->index(['producto_id', 'fecha_entrega']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
