<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {

            $table->id();

            // برای کاربران لاگین کرده
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            // برای کاربران مهمان
            $table->string('guest_id')->nullable();

            // محصول
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // تعداد
            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            // جلوگیری از ثبت دوباره همان محصول در سبد یک کاربر
            $table->unique(['user_id', 'product_id']);
            $table->unique(['guest_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
