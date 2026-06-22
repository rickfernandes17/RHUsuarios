<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostfixDomain extends Model
{
    // Define a conexão secundária
    protected $connection = 'mysql_postfix';

    protected $table = 'domain';

    protected $primaryKey = 'domain';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false; // PostfixAdmin usa colunas created/modified manualmente

    protected $fillable = [
        'domain',
        'description',
        'active',
        'created',
        'modified',
    ];
}
