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
        Schema::create('subscribe_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('total_ammount'); // unsignedBigInteger agar angkanya tidak bisa negatif (-)
            $table->boolean('is_paid');
            $table->date('subscription_start_date')->nullable(); // datanya bisa nullable, karna data ini akan terisi ketika super adminnya sudah approve pembayaran, maka subsnya akan aktif ketika sudah di approve oleh admin
            $table->string('proof'); // bukti pembayaran
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->softDeletes(); // deleted_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribe_transactions');
    }
};
