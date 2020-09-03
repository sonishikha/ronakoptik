<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'Vw_ItemMaster';

    public function price(){
        return $this->hasOne('App\Models\ItemPrice', 'Item_Code__c', 'ItemCode');
    }
}
