<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orgaos', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['tenant_id', 'name']);
        });

        Schema::table('fornecedores', function (Blueprint $table) {
            $table->dropUnique(['document']);
            $table->unique(['tenant_id', 'document']);
        });
    }

    public function down(): void
    {
        Schema::table('orgaos', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'name']);
            $table->unique(['name']);
        });

        Schema::table('fornecedores', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'document']);
            $table->unique(['document']);
        });
    }
};
