<?php

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('accounts');
            $table->foreignId('receiver_id')->nullable()->constrained('accounts');
            $table->string('type')->default(PaymentType::DEPOSIT);
            $table->double('amount')->comment('Transaction amount');
            $table->double('fee')->nullable()->comment('Exchange fee');
            $table->string('reference')->unique()->nullable(); // Unique transaction reference ID
            $table->string('status')->default(PaymentStatus::COMPLETED);
            $table->boolean('credit')->default(true);
            $table->text('remark')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
