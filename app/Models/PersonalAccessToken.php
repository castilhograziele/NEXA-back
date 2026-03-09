<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    // Aponta para a tabela no schema authentication
    protected $table = 'authentication.personal_access_tokens';
}