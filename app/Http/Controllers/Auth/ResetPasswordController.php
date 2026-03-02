<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function index($token, $email) {
        $isUserExist = User::where('email', '=', $email)->where('remember_token', '=', $token)->first();
        if (!$isUserExist) {
            return redirect()->route('forgot-password')->with('danger', 'You are trying to access an invalid link.');
        }
        return view('auth.reset-password',compact('token', 'email'));
    }

    public function resetPasswordAction(Request $request) {
        $password = $request['password'];
        $email = $request['reset_email'];
        $token = $request['reset_token'];

        $isUserExist = User::where('email', '=', $email)->where('remember_token', '=', $token)->first();
        if (!$isUserExist) {
            return redirect()->route('reset-password',['email'=>$email , 'token'=>$token])->with('danger', 'Something went wrong. Please try again');
        }
        $update = User::where('email', $email)->update(['password' => Hash::make($password), 'remember_token' => '']);
        
        return redirect()->route('login')->with('success', 'Please login with new password.');
    }
}
