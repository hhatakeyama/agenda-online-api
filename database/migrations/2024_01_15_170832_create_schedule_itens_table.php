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
        Schema::create('schedule_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("schedule_id")->nullable();
            $table->unsignedBigInteger("employee_id")->nullable();
            $table->unsignedBigInteger("service_id")->nullable();
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("price")->nullable();
            $table->string("duration")->nullable();
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('users');
            $table->foreign('schedule_id')->references('id')->on('schedules');
            $table->foreign('service_id')->references('id')->on('company_services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_itens');
    }
};
