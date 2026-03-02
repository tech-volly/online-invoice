<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LeadContact;
use App\Models\LeadFollowUp;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    public function contacts() {
        return $this->hasMany(LeadContact::class);
    }

    public function lead_follow_ups() {
        return $this->hasMany(LeadFollowUp::class);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($lead) {
            $lead->contacts()->delete();
            $lead->lead_follow_ups()->delete();
        });
    }


}
