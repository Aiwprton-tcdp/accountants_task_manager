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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('link', 1000);
            $table->foreignId('department_id')->constrained();
            $table->foreignId('contract_type_id')->constrained();
            $table->unsignedTinyInteger('payment_period_count');
            $table->enum('payment_period_type', ['d', 'w', 'm', 'q', 'y']);
            // $table->char('payment_period_type');
            $table->date('next_payment_date');
            $table->boolean('notified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};