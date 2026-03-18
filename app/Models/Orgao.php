<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orgao extends Model
{
    protected $table = 'orgaos';

    protected $fillable = ['name'];

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class);
    }
}
