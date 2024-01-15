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
        Schema::create('citys', function (Blueprint $table) {
            $table->integer('id', true);
			$table->char('state_id', 2)->nullable();
			$table->string('name')->nullable();
			$table->string('path')->nullable();
			$table->integer('priority')->nullable();
			$table->integer('ibge_id')->nullable();
			$table->timestamps();
            $table->foreign('state_id')->references('id')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citys');
    }
};
