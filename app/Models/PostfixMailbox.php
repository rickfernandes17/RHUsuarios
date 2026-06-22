<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostfixMailbox extends Model
{
    // Define a conexão secundária
    protected $connection = 'mysql_postfix';

    protected $table = 'mailbox';

    protected $primaryKey = 'username';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'name',
        'maildir',
        'quota',
        'domain',
        'active',
        'created',
        'modified',
    ];
}
