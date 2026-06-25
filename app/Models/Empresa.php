<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'nome',
        'dominio',
        'ou_dn',
        'usuarios_ou_dn',
        'grupos_ou_dn',
        'computadores_ou_dn',
        'servidores_ou_dn',
        'desativados_ou_dn',
    ];

    /**
     * Retorna os funcionários associados à empresa.
     */
    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionario::class, 'empresa_id');
    }
}
