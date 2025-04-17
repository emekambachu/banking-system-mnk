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
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->index()->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->index()->constrained('users')->onDelete('cascade');
            $table->float('amount')->index();
            $table->string('currency');
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->engine('InnoDB');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
    }
};
