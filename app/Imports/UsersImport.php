<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Mail\SendLoginDetailsMail;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Mail;
use Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $department = Department::whereName($row['department'])->first();
        $is_role_exist = Role::whereName($row['role'])->first();
        if($is_role_exist) {
            $role = $row['role'];
        }else {
            $role = 'User';
        }
        $password = generateRandomPassword();
        $user =  new User([
            'first_name' => $row['firstname'],
            'last_name' => $row['lastname'],
            'email' => $row['email'],
            'password' => Hash::make($password),
            'phone_number' => $row['phonenumber'],
            'department_id' => $department->id,
            'is_admin' => 0,
            'is_status' => 1,
            'is_verified' => 1,
            'failed_login_attempts' => 0
        ]);
        $user->assignRole($role);
        $details = [
            'userName' => $user->first_name.' '.$user->last_name,
            'userEmail' => $user->email,
            'password' => $password,
            'loginUrl' => url('/'),
        ];
        Mail::to($user->email)->send(new SendLoginDetailsMail($details))->cc(config('app.cc_admin_email'));
        $save_activity = [
            'email_sender' => config('app.from_email_address'),
            'email_receiver' => $user->email,
            'email_content' => 'Send login details to newly created user',
            'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $create_log = saveEmailActivity($save_activity);
        return $user;
    }
}
