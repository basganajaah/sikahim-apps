<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'gender')) {
                $table->boolean('gender')->nullable()->after('age');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(['age', 'gender'], fn ($col) => Schema::hasColumn('users', $col));
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
