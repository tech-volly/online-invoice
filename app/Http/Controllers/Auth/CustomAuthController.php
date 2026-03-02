<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\VerifyMail;
use App\Mail\BlockUserNotifyMail;
use App\Models\User;
use Session;
use Hash;
use Mail;
use Carbon\Carbon;
use Exception;

class CustomAuthController extends Controller
{

    public function index(){
        return view('auth.login');
    }  
      
    public function customLogin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ],
        [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',

        ]);
        $email = $request->email;
        if (auth()->attempt(array('email' => $email, 'password' => $request->password, 'is_status' => 1) )) {
            if(auth()->user()->failed_login_attempts >= 5) {
                $this->signOut();
                return redirect()->route('login')->withErrors('Your account is blocked. Please contact administrator.'); 
            }
            User::where('email',$email)->update(['failed_login_attempts' => 0]);
            // return redirect()->route('dashboard');
            auth()->user()->generateCode();
            return redirect()->route('verify-account');
        }else {
            $login = User::where('email','=',$email)->first();
            if(empty($login)){
                return redirect()->route('login')->withErrors('These credentials do not match our records.');
            }elseif($login->is_status == 0) {
                return redirect()->route('login')->withErrors('Your account is not active. Please contact the administrator.');
            }else {
                $loginAttempts = $login->failed_login_attempts;
                User::where('email',$email)->update(['failed_login_attempts' => $loginAttempts+= 1]);
                $user = User::where('email', $email)->first(); 
                if($user->failed_login_attempts >= 5) {
                    $details = [
                       'email' => $email,
                       'ip_address' => $_SERVER['SERVER_ADDR'],
                       'date_time' => getFormatedDateTime(Carbon::now()),
                       'login_attempts' => $user->failed_login_attempts
                    ];
                    
                    Mail::to(config('app.admin_email'))->cc(config('app.cc_admin_email'))->send(new BlockUserNotifyMail($details));
                    $save_activity = [
                        'email_sender' => config('app.from_email_address'),
                        'email_receiver' => config('app.admin_email'),
                        'email_content' => 'Multiple login attempts by user '.$email,
                        'email_send_date' => Carbon::now()->format('Y-m-d H:i:s')
                    ];
                    $create_log = saveEmailActivity($save_activity);
                    if (count(Mail::failures()) > 0) {
                        return back()->with('danger', 'Error in sending email to administrator.');
                    } else {
                        return redirect()->route('login')->withErrors('Too many login attempts. Please contact administrator.');    
                    }
                }   
                return redirect()->route('login')->withErrors('These credentials do not match our records.');
            }
        }
    }

    public function registration(){
        return view('auth.registration');
    }
      
    public function customRegistration(Request $request){  
        $request->validate([
            'name' => 'required|string|min:5',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ],
        [
            'name.required' => 'Userame is required',
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',

        ]);     
        $token = Str::random(60);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password =  Hash::make($request->password);
        $user->is_status = 0;
        $user->email_verified_token = $token;
        $user->is_admin = 0;
        $response = $user->save();
        $user->assignRole(2);
        
        if($user) {
            $verifyUrl = url('/') . '/user/verify/' . $token . '/' . urlencode($request->email);    
            $details = [
                'username' => $user->name,
                'verifyUrl' => $verifyUrl,
            ];
            Mail::to($user->email)->send(new VerifyMail($details));
            if (count(Mail::failures()) > 0) {
                return back()->with('danger', 'Error in sending email to your email address.');
            } else {
                return back()->with('success', 'You have registered successfully. We have sent a verification email to your registered email address.');
            }
        }
        return back()->with('danger', 'Error in registration. Please try again after sometime.');
    }

    public function verifyUser($token, $email){
        $isUserExist = User::where('email', '=', $email)->where('email_verified_token', '=', $token)->first();
        if ($isUserExist) {
            User::where('email', $email)->update(['is_status' => 1, 'email_verified_token' => '' ,'email_verified_at' => date('YmdHis')]);    
            return redirect()->route('login')->with('success', 'You can now login to your account.');
        }

        return redirect()->route('login')->with('danger', 'You are trying to access invalid url.');
    }

    public function signOut() {
        Session::flush();
        Auth::logout();
  
        return redirect()->route('login');
    }

    public function profile() {
        $user = Auth::user();

        return view('auth.profile', compact('user'));
    }

    public function profileAction(Request $request) {
        $post_array = $name = $request->post();
        $data = User::find(Auth::user()->id);
        $data->first_name = $post_array['first_name'];
        $data->last_name = $post_array['last_name'];
        $data->phone_number = $post_array['phone_number'];
        if (trim($post_array['password']) != "") {
            $data->password = Hash::make($post_array['password']);
        }
        $success = $data->save();

        if ($success) {
            $message = "Profile details updated successfully.";
            $message_class = "success";
        } else {
            $message = "Error in updating profile details. Please try again.";
            $message_class = "failure";
        }
        return redirect()->route('profile')->with($message_class, $message);

    }
}