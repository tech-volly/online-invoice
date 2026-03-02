<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\UserCode;

class TwoFAController extends Controller
{
    public function index() {
        if(!auth()->user()) {
            return redirect()->route('login')->with('danger', 'You are trying to access invalid url.');
        }
        return view('auth.two-fa-code');
    }

    public function store(Request $request) {
        $code = $request->otp_1.''.$request->otp_2.''.$request->otp_3.''.$request->otp_4;
        $userCodeExist = UserCode::where('user_id', auth()->user()->id)
            ->where('user_code', $code)
            ->first();
        if ($userCodeExist) {
            Session::put('user_2fa', auth()->user()->id);
            return redirect()->route('dashboard');
        }
  
        return back()->with('danger', 'You have entered wrong code.');
    }

    public function resend(){
        auth()->user()->generateCode();
  
        return back()->with('success', 'We sent you code on your email.');
    }
}
