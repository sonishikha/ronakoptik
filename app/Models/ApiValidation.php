<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use Exception;

class ApiValidation extends Model
{
    public function validateAndGetUser($request, $offset = false, $sync = false)
    {
        $validator = $this->validateRequest($request, $offset, $sync);
        if($validator->fails()){
            throw New Exception($validator->messages()->first());
        }
        
        $user = $this->checkUserExistsByEmail($request->userName);
        if(!$user) {
            throw new Exception('Username Not Found');
        };
        
        return $user;
    }

    public function validateRequest($request, $offset, $sync)
    {
        $validator = Validator::make($request->all(), 
                    array(
                        'userName' => 'required|email',
                    )
        );
        $validator->sometimes('offSet', 'required|int', function () use ($offset) {
            return $offset === true;
        });
        $validator->sometimes('sync', ['required', Rule::in(['no','yes','No','Yes','NO','YES']),], function () use ($sync) {
            return $sync === true;
        });
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
