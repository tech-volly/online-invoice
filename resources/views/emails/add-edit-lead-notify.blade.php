<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Followup Reminder</title>
</head>
<style>
    @import url(http://fonts.cdnfonts.com/css/circular-std);
    @import url(https://fonts.googleapis.com/icon?family=Material+Icons);

</style>

<body style="background: #f1f1f1; padding: 15px 15px; font-family: 'Circular Std', sans-serif;">
    <table align="center" border="0" cellpadding="0" cellspacing="0"
           width="550" style="background: #fff;
           border-radius: 15px; max-width: 500px; padding: 20px 30px;
           position: absolute;left: 50%;top: 50%;
           transform: translate(-50%, -50% );overflow: hidden;text-align: center; width: 80%;">
        <tbody>
            <tr>
                <td align="center">
                    <table align="center" border="0" cellpadding="0" 
                           cellspacing="0" class="col-550" width="550">
                        <tbody>
                            <tr>
                                <td align="center">
                                    <img src="{{ URL::asset('public/assets/img/logo2.png')}}?28062022" alt="logo" style="width: 100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>       
            </tr>
            <tr>
                <td align="left">
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 15px;">Dear User,</p>      
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 15px;">Lead is {{$details['action']}} by {{ $details['loggedin_user'] }}. </p>  
                </td>
            </tr>
            <tr>
                <td align="left">
                    @if($details['clientName'])
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Client Name: </strong> {{$details['clientName']}}</p>   
                    @endif
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Company Name: </strong> {{$details['companyName']}}</p>   
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Discussion Date: </strong> {{ $details['lead_followup_details']->lead_discussion_date ? getFormatedDate($details['lead_followup_details']->lead_discussion_date) : '' }}</p>   
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Followup Date: </strong> {{ $details['lead_followup_details']->followup_datetime ? getFormatedDate($details['lead_followup_details']->followup_datetime) : '' }}</p>   
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Created By: </strong> {{ $details['lead_followup_details']->lead_created_by ? $details['lead_followup_details']->lead_created_by : '' }}</p>   
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Followup Notes: </strong> {!! $details['lead_followup_details']->followup_notes !!}</p>   


                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>    