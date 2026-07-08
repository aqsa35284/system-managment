<?php

namespace App\Models;

use App\Support\PlanFeatures;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity; // 🟢 1. LOGS ACTIVITY IMPORT KI GAYI HAI

class Company extends Model
{
    use LogsActivity; // 🟢 2. YAHAN TRAIT ADD KI GAYI HAI

    // 🚀 NAYA COLUMN 'features' FILLABLE MEIN ADD KIYA HAI
    protected $fillable = [
        'name', 'email', 'phone', 'status', 'payment_status', 
        'subscription_ends_at', 'max_employees', 'plan_type','partner_id',
        'features' 
    ];

    // 🚀 DATABASE KE JSON TEXT KO ARRAY MEIN CONVERT KARNE KE LIYE
    protected $casts = [
        'features' => 'array',
    ];

    // Ek company ke bohot se users (admins/employees) hote hain
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Custom check: Kya subscription valid hai?
    public function isValid()
    {
        return $this->status === 'active' && $this->subscription_ends_at >= now();
    }

    // Ek company ke bohot se employees ho sakte hain
    public function employees()
    {
        return $this->hasMany(User::class, 'company_id')->where('role', 'employee');
    }

    // Ek company ke admins
    public function admins()
    {
        return $this->hasMany(User::class, 'company_id')->where('role', 'admin');
    }

    /**
     * 🚀 SaaS Feature Checker (DYNAMIC DB ENGINE)
     * Ab yeh direct Database se check karega ke Super Admin / Partner ne 
     * form mein konsay checkboxes tick kiye hain.
     */
    public function hasFeature($feature_name): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return PlanFeatures::matches($feature_name, $this->features ?? []);
    }

    public function syncFeaturesFromPlan(?string $planSlug = null): void
    {
        $slug = $planSlug ?? $this->plan_type;
        $this->update(['features' => PlanFeatures::forPlan($slug)]);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_type', 'slug');
    }

    // Yeh function batayega ke ek Partner ke paas bohut sari companies (tenants) ho sakti hain
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
}