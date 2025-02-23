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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number')->nullable();
            $table->double('opening_balance')->comment('Cash at the start of the day'); //
            $table->double('closing_balance')->nullable()->comment('Cash at the end of the day');
            $table->double('current_balance')->default(0)->comment('Live balance');
            $table->boolean('default')->default(false);
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
