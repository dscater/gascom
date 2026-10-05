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
        Schema::create('pago_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pago_id");
            $table->unsignedBigInteger("gasto_id");
            $table->decimal("monto", 24, 2)->default(0);
            $table->date("fecha")->nullable();
            $table->timestamps();

            $table->foreign("pago_id")->on("pagos")->references("id");
            $table->foreign("gasto_id")->on("gastos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_detalles');
    }
};
