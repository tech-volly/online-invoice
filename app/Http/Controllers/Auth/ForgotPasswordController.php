<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Mail;

class ForgotPasswordController extends Controller
{
    public function index() {
        return view('auth.forgot-password');
    }

    public function forgotPasswordAction(Request $request) {
        $email = $request->only('email');
        $isEmailExist = User::whereEmail($email)->first();

        if(!$isEmailExist) {
            return back()->with('danger', 'Your email is not associated with us.');
        }

        $token = Str::random(60);
        $affected = User::where('email', '=', $email)->update(['remember_token' => $token]);
        
        $resetUrl = url('/') . '/reset-password/' . $token . '/' . urlencode($isEmailExist->email);
        $details = [
            'username' => $isEmailExist->name,
            'resetUrl' => $resetUrl,
        ];

        Mail::to($email)->cc(config('app.cc_admin_email'))->send(new ResetPasswordMail($details));
        $save_activity = [
            'email_sender' => config('app.from_email_address'),
            'email_receiver' => $isEmailExist->email,
            'email_content' => 'Send forgot password email',
            'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $create_log = saveEmailActivity($save_activity);
        if (count(Mail::failures()) > 0) {
            return back()->with('danger', 'Failed to send password reset email, please try again.');
        } else {
            return back()->with('success', 'A reset link has been sent to your email address.');
        }
    }
}
