<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstimateSetting;

class EstimateSettingController extends Controller
{
    function __construct(){
        $this->middleware('permission:estimate-setting-list', ['only' => ['index']]);
    }

    public function index() {
        $data = EstimateSetting::first();
        return view('masters.estimate-settings.index', compact('data'));
    }

    public function saveEstimateSettings(Request $request) {
        $post_array = $request->post();        
        $id = (isset($post_array['id']) && $post_array['id'] > 0) ? $post_array['id'] : 0;
        if($id == 0) {
            $estimate_setting = new EstimateSetting();
        }else if ($id > 0) {
            $estimate_setting = EstimateSetting::find($id);
        }
        $estimate_setting->estimate_header_company_name = $post_array['estimate_header_company_name'];
        $estimate_setting->estimate_header_number = $post_array['estimate_header_number'];
        $estimate_setting->estimate_header_street_address = $post_array['estimate_header_street_address'];
        $estimate_setting->estimate_header_city = $post_array['estimate_header_city'];
        $estimate_setting->estimate_header_state = $post_array['estimate_header_state'];
        $estimate_setting->estimate_header_postalcode = $post_array['estimate_header_postalcode'];
        $estimate_setting->estimate_header_tollfree = $post_array['estimate_header_tollfree'];
        $estimate_setting->estimate_header_email = $post_array['estimate_header_email'];
        $estimate_setting->estimate_header_website = $post_array['estimate_header_website'];
        $estimate_setting->estimate_footer_company_name = $post_array['estimate_footer_company_name'];
        $estimate_setting->estimate_footer_bsb_number = $post_array['estimate_footer_bsb_number'];
        $estimate_setting->estimate_footer_acc_number = $post_array['estimate_footer_acc_number'];
        $estimate_setting->estimate_footer_email = $post_array['estimate_footer_email'];
        $estimate_setting->estimate_footer_notes = $post_array['estimate_footer_notes'];
        $response = $estimate_setting->save();

        if($response) {
            if($id == 0) {
                $message = "Estimate Settings are added successfully.";
            }else if ($id > 0) {
                $message = "Estimate Settings are updated successfully.";
            }
            $message_class = "success";
        }else {
            if ($id == 0) {
                $message = "Error in adding Estimate Settings. Please try again.";
            } else if ($id > 0) {
                $message = "Error in updating Estimate Settings. Please try again.";
            }
            $message_class = "danger";
        }

        return redirect()->route('estimate-settings')->with($message_class, $message);
    }
}
