<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orgaos', fn (Blueprint $t) =>
            $t->string('tenant_id')->nullable()->index()->after('id'));

        Schema::table('fornecedores', fn (Blueprint $t) =>
            $t->string('tenant_id')->nullable()->index()->after('id'));

        Schema::table('despesas', fn (Blueprint $t) =>
            $t->string('tenant_id')->nullable()->index()->after('id'));
    }

    public function down(): void
    {
        Schema::table('orgaos', fn (Blueprint $t) => $t->dropColumn('tenant_id'));
        Schema::table('fornecedores', fn (Blueprint $t) => $t->dropColumn('tenant_id'));
        Schema::table('despesas', fn (Blueprint $t) => $t->dropColumn('tenant_id'));
    }
};
