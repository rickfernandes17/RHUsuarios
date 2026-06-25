<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Funcionario extends Model
{
    use HasFactory;

    protected $table = 'funcionarios';

    protected $fillable = [
        'empresa_id',
        'nome',
        'sobrenome',
        'cpf',
        'cargo',
        'email_corporativo',
        'login_rede',
        'user_dn',
    ];

    /**
     * Retorna a empresa do funcionário.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
