<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('source');
            $table->text('description');
            $table->decimal('value', 10, 2)->default(0);
            $table->boolean('is_receipt')->default(false);
            $table->boolean('is_debt')->default(false);
            $table->boolean('is_fixed')->default(false);
            $table->boolean('is_variable')->default(false);
            $table->boolean('is_seasonal')->default(false);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
