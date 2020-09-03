<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use Exception;

class ApiValidation extends Model
{
    public function validateAndGetUser($request)
    {
        $validator = $this->validateRequest($request);
        if($validator->fails()){
            throw New Exception($validator->messages()->first());
        }
        
        $user = $this->checkUserExistsByEmail($request->userName);
        if(!$user) {
            throw new Exception('Username Not Found');
        };
        
        return $user;
    }

    public function validateRequest($request)
    {
        $validator = Validator::make($request->all(), 
                    array(
                        "userName" => "required|email",
                        "offSet" => "required|int",
                        "sync" => ["required", Rule::in(['no','yes',"No","Yes","NO","YES"]),],
                    )
        );
        return $validator;
    }

    public function checkUserExistsByEmail($email)
    {
        return User::where('email', '=', $email)->first();
    }

    public function getUserRegion($user_id)
    {
        return User::find($user_id)->region;
    }

    public function getUserBrand($user_id)
    {
        return User::find($user_id)->brand;
    }

    public function getUserWarehouse($user_id)
    {
        return User::find($user_id)->warehouse;
    }
}
