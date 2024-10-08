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
        Schema::create('postulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->integer('radicado')->nullable();
            $table->enum('status',['postulado','docs_aprobados','contacto','aprobado','rechazado'])->default('postulado');
            $table->text('description')->nullable();
            $table->json('payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulations');
    }
};
