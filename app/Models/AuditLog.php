<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Yeh property add karna lazmi hai
    protected $fillable = [
        'super_admin_id',
        'company_id',
        'action',
        'url',
        'ip'
    ];
    public function admin() {
    return $this->belongsTo(\App\Models\User::class, 'super_admin_id');
}

public function company() {
    return $this->belongsTo(\App\Models\Company::class, 'company_id');
}
}