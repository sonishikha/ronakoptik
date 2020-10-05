<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $connection = 'mysql';
    protected $table = 'ro_core_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email
        ];
    }
    
    public function group_code(){
        return $this->hasOne('App\Models\UserRegion', 'user_id');
    }

    public function warehouse(){
        return $this->hasMany('App\Models\UserWarehouseMap', 'user_id');
    }

    public function brand(){
        return $this->hasMany('App\Models\UserBrandMap', 'user_id');
    }


    // public function __destruct(){
    //     //\DB::purge($this->connection);
    //     \Log::info('Mysql Disconnection:',['connection_name'=>$this->connection,
    //         'db_name'=>\DB::connection($this->connection)->getDatabaseName(),
    //         'purge_value'=>\DB::purge($this->connection)]
    //     );
    // }

}
