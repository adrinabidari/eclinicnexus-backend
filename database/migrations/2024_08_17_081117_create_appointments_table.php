<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('doctor_id');
            $table->integer('patient_id');
            $table->integer('service_id');
            $table->string('date');
            $table->integer('day');
            $table->integer('time_slot_id');
            $table->string('status')->default('booked');
            $table->integer('payment')->default('0');
            $table->string('payment_method');
            $table->longText('description')->nullable();
            $table->double('amount');
            $table->double('additional_fee')->nullable();
            $table->double('total_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
