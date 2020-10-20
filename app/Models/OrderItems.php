<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $connection = 'mysql';
    protected $table = 'ro_tran_ordr_item';
    public $timestamps = false;

    public function __destruct(){
        \Log::info('Mysql Disconnection:',['connection_name'=>$this->connection,
            'db_name'=>\DB::connection($this->connection)->getDatabaseName(),
            'purge_value'=>\DB::purge($this->connection)]
        );
    }
}
