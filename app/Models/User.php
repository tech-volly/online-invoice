<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserCode;
use App\Models\Department;
use Exception;
use Mail;
use Carbon\Carbon;
use App\Mail\SendCodeMail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',       
        'email',
        'password',
        'phone_number',
        'department_id',
        'is_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function generateCode()
    {
        $code = rand(1000, 9999);
  
        UserCode::updateOrCreate(
            [ 'user_id' => auth()->user()->id ],
            [ 'user_code' => $code ]
        );
    
        try {
            $details = [
                'title' => 'HDS Financials',
                'code' => $code
            ];    
            if(app()->environment() == "production") {
                Mail::to(auth()->user()->email)->cc(config('app.cc_admin_email'))->send(new SendCodeMail($details));
            }
            // $save_activity = [
            //     'email_sender' => config('app.from_email_address'),
            //     'email_receiver' => auth()->user()->email,
            //     'email_content' => 'Send 2FA code for authentication',
            //     'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
            // ];
            // $create_log = saveEmailActivity($save_activity);
        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

}
