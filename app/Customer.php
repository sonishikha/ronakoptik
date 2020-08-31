<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The view associated with the model.
     *
     * @var string
     */
    protected $table = 'Vw_CustomerMaster';

    public function ageing(){
        return $this->hasOne('App\Models\CustomerAgeing', 'BP_Code__c', 'Customer Code');
    }

    public function pdc(){
        return $this->hasMany('App\Models\PdcOnHold', 'BP_Code__c', 'Customer_Code__c');
    }
}
