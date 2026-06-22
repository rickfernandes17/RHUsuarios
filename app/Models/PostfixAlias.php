<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostfixAlias extends Model
{
    // Define a conexão secundária
    protected $connection = 'mysql_postfix';

    protected $table = 'alias';

    protected $primaryKey = 'address';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'address',
        'goto',
        'domain',
        'active',
        'created',
        'modified',
    ];
}
