<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        // 1. AUTOMATIC FILTER (Read): Data fetch karte waqt khud hi company_id lag jayegi
        if (Auth::check() && Auth::user()->role !== 'super_admin') {
            static::addGlobalScope('company_id', function (Builder $builder) {
                $builder->where('company_id', Auth::user()->company_id);
            });
        }

        // 2. AUTOMATIC SAVE (Create): Naya data save karte waqt khud hi company_id insert ho jayegi
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->role !== 'super_admin' && empty($model->company_id)) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}