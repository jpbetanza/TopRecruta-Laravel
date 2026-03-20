<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orgao extends Model
{
    protected $table = 'orgaos';

    protected $fillable = ['name', 'tenant_id'];

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

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class);
    }
}
