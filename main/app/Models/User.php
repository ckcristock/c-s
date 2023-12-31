<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario',
        'person_id',
        'menu',
        'change_password',
        'password_updated_at',
        'state',
        'board_id',
        /* 'nombres',
        'apellidos',
        'password', */
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function person()
    {
        return $this->belongsTo(Person::class)->fullName();
    }

    public function personName()
    {
        return $this->belongsTo(Person::class, 'person_id', 'id')->completeName();
    }

    protected $casts = [
        'menu' => 'array'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function menu() {
        return $this->hasMany(MenuPermissionUsuario::class, 'usuario_id', 'id')->with('menuPermission');
    }

}
