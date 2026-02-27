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
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolum role
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');

            // Tambah username (jika tidak wujud dan diperlukan)
            $table->string('username')->unique()->nullable()->after('name'); 

            // Jika anda mahu menukar nama 'id' kepada 'userID', ini lebih kompleks
            // Untuk sementara, kekalkan nama 'id' dan gunakan ia sebagai FK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'username']);
        });
    }
};