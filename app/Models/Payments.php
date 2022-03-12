<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payments extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_id', 'order_id', 'refund_id',
    ];
}
