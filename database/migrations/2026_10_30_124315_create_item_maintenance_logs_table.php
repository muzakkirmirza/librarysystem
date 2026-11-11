<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items');
            $table->text('issue_description');
            $table->enum('status', ['in_repair', 'fixed'])->default('in_repair');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_maintenance_logs');
    }
};