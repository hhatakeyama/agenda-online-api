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
        Schema::create('scheduling', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("company_id")->nullable();   
            $table->unsignedBigInteger("client_id")->nullable();
            $table->string("date")->nullable();
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("price")->nullable();
            $table->string("duration")->nullable();
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
        Schema::dropIfExists('scheduling');
    }
};
