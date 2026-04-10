<?php

namespace App\Models\Passport;

use Laravel\Passport\Client as PassportClient;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Client extends PassportClient
{
    use HasUuids;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';
}
