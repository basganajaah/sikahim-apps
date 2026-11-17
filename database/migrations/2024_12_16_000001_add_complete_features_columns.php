<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        Schema::table('kegiatan', function (Blueprint $table) {
            if (! Schema::hasColumn('kegiatan', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verification');
            }
        });

        Schema::table('pendaftaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pendaftaran', 'notes')) {
                $table->text('notes')->nullable()->after('registration_status');
            }
        });

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 100);
                $table->string('target_type', 100)->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->text('description');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');

        Schema::table('pendaftaran', function (Blueprint $table) {
            if (Schema::hasColumn('pendaftaran', 'notes')) {
                $table->dropColumn('notes');
            }
        });

        Schema::table('kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('kegiatan', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
