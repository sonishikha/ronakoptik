<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $connection = 'mysql';
    protected $table = 'ro_tran_ordr_item';
    public $timestamps = false;

}
