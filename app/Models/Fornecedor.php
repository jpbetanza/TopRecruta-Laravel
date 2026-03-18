<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';

    protected $fillable = ['name', 'document'];

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class);
    }
}
