<?php

namespace Database\Seeders;

use App\Models\Orgao;
use App\Models\Fornecedor;
use App\Models\Despesa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tenants = $this->parseTenants();

        if (empty($tenants)) {
            $this->command->warn('API_KEYS não configurado. Nenhum dado será gerado.');
            return;
        }

        foreach ($tenants as $alias) {
            app()->instance('tenant_id', $alias);

            $exists = Orgao::withoutGlobalScopes()->where('tenant_id', $alias)->exists();

            if ($exists) {
                $this->command->info("Tenant [{$alias}] já possui dados. Pulando.");
                continue;
            }

            $this->command->info("Seeding tenant: {$alias}");
            $this->seedTenant();
        }

        // Unbind after seeding
        app()->forgetInstance('tenant_id');
    }

    private function seedTenant(): void
    {
        $saude    = Orgao::create(['name' => 'Secretaria de Saúde']);
        $educacao = Orgao::create(['name' => 'Secretaria de Educação']);
        $transito = Orgao::create(['name' => 'Secretaria de Trânsito']);

        $limpeza = Fornecedor::create(['name' => 'Limpa Natal Ltda',       'document' => '12.345.678/0001-90']);
        $tecnologia = Fornecedor::create(['name' => 'TechSol Informática', 'document' => '98.765.432/0001-10']);
        $transporte = Fornecedor::create(['name' => 'TransNatal Transportes', 'document' => '11.222.333/0001-44']);
        $hospitalar = Fornecedor::create(['name' => 'MedSupply Hospitalar', 'document' => '55.666.777/0001-88']);

        $despesas = [
            [$saude->id,    $hospitalar->id, 'Compra de materiais hospitalares',        15000.00],
            [$saude->id,    $limpeza->id,    'Serviço de limpeza nas UBS',               8500.00],
            [$saude->id,    $tecnologia->id, 'Manutenção do sistema de prontuários',    12000.00],
            [$educacao->id, $tecnologia->id, 'Compra de computadores para escolas',     45000.00],
            [$educacao->id, $limpeza->id,    'Limpeza de escolas municipais',            6000.00],
            [$educacao->id, $transporte->id, 'Transporte escolar',                      22000.00],
            [$transito->id, $tecnologia->id, 'Instalação de câmeras de monitoramento',  35000.00],
            [$transito->id, $transporte->id, 'Manutenção de semáforos',                 18000.00],
            [$transito->id, $limpeza->id,    'Limpeza e conservação de vias',            9500.00],
        ];

        foreach ($despesas as [$orgao_id, $fornecedor_id, $descricao, $valor]) {
            Despesa::create(compact('orgao_id', 'fornecedor_id', 'descricao', 'valor'));
        }
    }

    private function parseTenants(): array
    {
        $raw = env('API_KEYS', '');

        if (empty($raw)) {
            return [];
        }

        return collect(explode(',', $raw))
            ->map(fn ($entry) => explode(':', trim($entry), 2)[0])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
