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
        Schema::create('participante_gastos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("participante_id");
            $table->unsignedBigInteger("gasto_id");
            $table->double("porcentaje", 11, 8);
            $table->timestamps();

            $table->foreign("participante_id")->on("participantes")->references("id");
            $table->foreign("gasto_id")->on("gastos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participante_gastos');
    }
};
