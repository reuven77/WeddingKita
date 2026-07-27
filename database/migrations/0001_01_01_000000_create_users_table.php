<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Skema users sesuai 02-PRD.md §5:
     * - UUID primary key
     * - role enum: member | admin
     * - phone (nullable)
     * - timestamps pakai TIMESTAMPTZ (via Laravel default di pgsql)
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 300);
            $table->string('email', 300)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('member'); // enum: member, admin — pakai string+check
            $table->string('phone', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // CHECK constraint untuk role enum
        DB::statement("ALTER TABLE users ADD CONSTRAINT check_user_role CHECK (role IN ('member', 'admin'))");


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
