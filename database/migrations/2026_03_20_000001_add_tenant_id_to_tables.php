<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orgaos', function (Blueprint $t) {
            $t->string('tenant_id')->nullable()->index()->after('id');
            $t->unique(['tenant_id', 'name']);
        });

        Schema::table('fornecedores', function (Blueprint $t) {
            $t->string('tenant_id')->nullable()->index()->after('id');
            $t->unique(['tenant_id', 'document']);
        });

        Schema::table('despesas', fn (Blueprint $t) =>
            $t->string('tenant_id')->nullable()->index()->after('id'));
    }

    public function down(): void
    {
        Schema::table('orgaos', function (Blueprint $t) {
            $t->dropUnique(['tenant_id', 'name']);
            $t->dropColumn('tenant_id');
        });
        Schema::table('fornecedores', function (Blueprint $t) {
            $t->dropUnique(['tenant_id', 'document']);
            $t->dropColumn('tenant_id');
        });
        Schema::table('despesas', fn (Blueprint $t) => $t->dropColumn('tenant_id'));
    }
};
