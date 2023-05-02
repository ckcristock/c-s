<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPermissionUsuario extends Model
{
    use HasFactory;
    protected $table = 'menu_permission_usuario';
    protected $fillable = [
        'menu_permission_id',
        'usuario_id'
    ];
}
