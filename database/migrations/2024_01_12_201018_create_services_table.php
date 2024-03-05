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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('serviceCategory_id');
            $table->decimal('price', 10, 2);
            $table->string('duration');
            $table->string('email_message')->nullable();
            $table->string('sms_message')->nullable();
            $table->tinyInteger('send_email')->default(0);
            $table->tinyInteger('send_sms')->default(0);
            $table->tinyInteger('can_choose_random')->default(1);
            $table->tinyInteger('can_choose_employee')->default(1);
            $table->tinyInteger('can_simultaneous')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('serviceCategory_id')->references('id')->on('service_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
