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
    Schema::create('loan_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('borrow_request_id')->constrained('borrow_requests')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
        $table->date('issue_date');
        $table->date('due_date');
        $table->date('return_date')->nullable();
        $table->enum('status', ['issued', 'returned', 'overdue'])->default('issued');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_transactions');
    }
};
