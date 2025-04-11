<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'old_prod';

    protected $table = 'users';
}
