<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" style="border: 1px solid #ff9b44;">
    <tr>
        <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <a href="{{url('/')}}"><img style="padding: 10px;" width="100" src="{{ URL::asset('public/assets/img/logo2.png')}}?28062022"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tbody>
                    <tr>
                        <td style="text-align: center; font-size: 16px; padding-top: 20px;">
                            <p style="padding: 0px 10px;">Dear {{$details['username']}}</p>
                            <p style="padding: 0px 10px;">Thank you for registering at HDS Financials</p>
                            <p style="padding: 0px 10px;">You can now activate your account by clicking the button below!</p>
                            <p style="padding: 0px 10px;">If you’re having trouble clicking the "Activate Account" button, copy and paste the URL below into your web browser</p>
                            <a href="{{$details['verifyUrl']}}" style="text-decoration: none;"><span style="color: #0378B5">{{$details['verifyUrl']}}</span></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center; padding: 15px;">
                            <a href="{{$details['verifyUrl']}}" style="background: linear-gradient(to right, #ff9b44 0%, #fc6075 100%); border: none; color: white; padding: 14px 16px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 27px;">Activate Account</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="line-height: 100px; text-align: center; font-size: 16px;">
                            <p>Best wishes, HDS Financials Team.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tbody>
                    <tr>
                        <td style="text-align: center; line-height: 50px;">
                            <a href="{{url('/')}}" style="text-decoration: none;"><span style="color: #0378B5">{{url('/')}}</span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
