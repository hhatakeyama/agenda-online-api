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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('district');
            $table->string('cep');
            $table->integer('city_id');
            $table->string('state')->max(2);
            $table->string('thumb')->nullable();
            $table->unsignedBigInteger('organization_id');
            $table->string('email');
            $table->string('phone');
            $table->string('mobilePhone')->nullable();
            $table->longText('socialMedia')->nullable();
            $table->longText('map')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
