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

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Órgãos (Secretarias da Prefeitura)
        $saude = Orgao::create(['name' => 'Secretaria de Saúde']);
        $educacao = Orgao::create(['name' => 'Secretaria de Educação']);
        $transito = Orgao::create(['name' => 'Secretaria de Trânsito']);

        // Fornecedores
        $limpeza = Fornecedor::create([
            'name' => 'Limpa Natal Ltda',
            'document' => '12.345.678/0001-90',
        ]);

        $tecnologia = Fornecedor::create([
            'name' => 'TechSol Informática',
            'document' => '98.765.432/0001-10',
        ]);

        $transporte = Fornecedor::create([
            'name' => 'TransNatal Transportes',
            'document' => '11.222.333/0001-44',
        ]);

        $hospitalar = Fornecedor::create([
            'name' => 'MedSupply Hospitalar',
            'document' => '55.666.777/0001-88',
        ]);

        // Despesas
        Despesa::create([
            'orgao_id' => $saude->id,
            'fornecedor_id' => $hospitalar->id,
            'descricao' => 'Compra de materiais hospitalares',
            'valor' => 15000.00,
        ]);

        Despesa::create([
            'orgao_id' => $saude->id,
            'fornecedor_id' => $limpeza->id,
            'descricao' => 'Serviço de limpeza nas UBS',
            'valor' => 8500.00,
        ]);

        Despesa::create([
            'orgao_id' => $saude->id,
            'fornecedor_id' => $tecnologia->id,
            'descricao' => 'Manutenção do sistema de prontuários',
            'valor' => 12000.00,
        ]);

        Despesa::create([
            'orgao_id' => $educacao->id,
            'fornecedor_id' => $tecnologia->id,
            'descricao' => 'Compra de computadores para escolas',
            'valor' => 45000.00,
        ]);

        Despesa::create([
            'orgao_id' => $educacao->id,
            'fornecedor_id' => $limpeza->id,
            'descricao' => 'Limpeza de escolas municipais',
            'valor' => 6000.00,
        ]);

        Despesa::create([
            'orgao_id' => $educacao->id,
            'fornecedor_id' => $transporte->id,
            'descricao' => 'Transporte escolar',
            'valor' => 22000.00,
        ]);

        Despesa::create([
            'orgao_id' => $transito->id,
            'fornecedor_id' => $tecnologia->id,
            'descricao' => 'Instalação de câmeras de monitoramento',
            'valor' => 35000.00,
        ]);

        Despesa::create([
            'orgao_id' => $transito->id,
            'fornecedor_id' => $transporte->id,
            'descricao' => 'Manutenção de semáforos',
            'valor' => 18000.00,
        ]);

        Despesa::create([
            'orgao_id' => $transito->id,
            'fornecedor_id' => $limpeza->id,
            'descricao' => 'Limpeza e conservação de vias',
            'valor' => 9500.00,
        ]);
    }
}
