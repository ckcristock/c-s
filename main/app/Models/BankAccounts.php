<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
    use HasFactory;
    protected $table = 'bank_accounts';
    protected $fillable = [
        'name',
        'associated_account',
        'account_number',
        'balance',
        'status',
        'type',
        'description'
    ];
}
