<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('log_disparos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // 'email', 'whatsapp', 'sms'
            $table->string('destinatario');
            $table->text('assunto')->nullable();
            $table->text('mensagem')->nullable();
            $table->string('status'); // 'pendente', 'enviado', 'falha', 'agendado'
            $table->timestamp('data_agendamento')->nullable();
            $table->timestamp('data_envio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('log_disparos');
    }
};
