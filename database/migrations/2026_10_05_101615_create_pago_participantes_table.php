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
        Schema::create('pago_participantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pago_id");
            $table->unsignedBigInteger("gasto_id");
            $table->unsignedBigInteger("pago_detalle_id");
            $table->unsignedBigInteger("participante_id");
            $table->decimal("monto", 24, 2)->default(0);
            $table->double("porcentaje", 8, 2)->default(0);
            $table->timestamps();

            $table->foreign("pago_id")->on("pagos")->references("id");
            $table->foreign("gasto_id")->on("gastos")->references("id");
            $table->foreign("pago_detalle_id")->on("pago_detalles")->references("id");
            $table->foreign("participante_id")->on("participantes")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_participantes');
    }
};
