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
        Schema::create('company_days_of_weeks', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger("day_of_week")->default(0);
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("start_time_2")->nullable();
            $table->string("end_time_2")->nullable();
            $table->string("start_time_3")->nullable();
            $table->string("end_time_3")->nullable();
            $table->string("start_time_4")->nullable();
            $table->string("end_time_4")->nullable();
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
        Schema::dropIfExists('company_days_of_weeks');
    }
};
