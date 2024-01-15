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
        Schema::create('days_of_week_company', function (Blueprint $table) {
            $table->id();
            $table->string("day_of_week")->nullable();
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("start_time_2")->nullable();
            $table->string("end_time_2")->nullable();
            $table->unsignedBigInteger("company_id")->nullable();            
            $table->timestamps();

            $table->foreign("company_id")->references("id")->on("companies")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days_of_week_company');
    }
};
