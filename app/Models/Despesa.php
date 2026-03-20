<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    protected $table = 'despesas';

    protected $fillable = [
        'tenant_id',
        'orgao_id',
        'fornecedor_id',
        'descricao',
        'valor',
        'comprovante_path',
        'comprovante_mime',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (app()->bound('tenant_id')) {
                $query->where('tenant_id', app('tenant_id'));
            }
        });

        static::creating(function ($model) {
            if (app()->bound('tenant_id')) {
                $model->tenant_id = app('tenant_id');
            }
        });
    }

    public function orgao(): BelongsTo
    {
        return $this->belongsTo(Orgao::class);
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }
}
