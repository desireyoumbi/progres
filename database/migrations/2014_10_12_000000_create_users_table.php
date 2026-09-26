<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nom complet du membre
            $table->string('phone')->unique(); // identifiant de connexion + référence Mobile Money
            $table->string('email')->nullable()->unique(); // optionnel, pas tout le monde n'en a
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role', ['president', 'treasurer', 'secretary', 'member'])->default('member');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
       
    }
};