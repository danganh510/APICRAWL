<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScProductTest extends Model
{
    use HasFactory;

    protected $table = 'sc_product_test';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'product_name',
        'product_price',
        'product_rate',
    ];

}
