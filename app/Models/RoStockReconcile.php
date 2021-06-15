<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoStockReconcile extends Model
{

    protected $connection = 'mysql';
    protected $table = 'ro_new_stock_reconcile';
    public $timestamps = false;
}
