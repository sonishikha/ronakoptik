<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mysql';
    protected $table = 'ro_tran_ordr_data';
    public $timestamps = false;

    public function OrderItems(){
        return $this->hasMany('App\Models\OrderItems', 'tran_id');
    }

    public function __destruct(){
        \Log::info('Mysql Disconnection:',['connection_name'=>$this->connection,
            'db_name'=>\DB::connection($this->connection)->getDatabaseName(),
            'purge_value'=>\DB::purge($this->connection)]
        );
    }
}
