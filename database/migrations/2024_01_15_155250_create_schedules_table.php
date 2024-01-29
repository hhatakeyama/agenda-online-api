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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("company_id");   
            $table->unsignedBigInteger("client_id");
            $table->boolean('confirmed')->default(false);
            $table->boolean('done')->default(false);
            $table->string("date");
            $table->string("confirmed_hash");
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule');
    }
};
