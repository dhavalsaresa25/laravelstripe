<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCard extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id', 'name_on_card', 'card_number', 'CVC', 'expiration_month','expiration_year',
    ];
}
