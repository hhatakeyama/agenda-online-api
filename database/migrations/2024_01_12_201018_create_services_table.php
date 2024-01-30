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
            $table->string('description');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('serviceCategory_id');
            $table->decimal('price', 10, 2);
            $table->string('duration');
            $table->boolean('send_email')->default(false);
            $table->boolean('send_sms')->default(false);
            $table->string('email_message')->nullable();
            $table->string('sms_message')->nullable();
            $table->boolean('can_choose_random')->default(false);
            $table->boolean('can_choose_employee')->default(true);
            $table->boolean('can_simultaneous')->default(false);
            $table->boolean('status')->default(true);
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
