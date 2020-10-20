<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBrandMap extends Model
{
    protected $connection = 'mysql';
    protected $table = 'ro_user_brand_map';
    
    public function __destruct(){
        \Log::info('Mysql Disconnection:',['connection_name'=>$this->connection,
            'db_name'=>\DB::connection($this->connection)->getDatabaseName(),
            'purge_value'=>\DB::purge($this->connection)]
        );
    }

}
