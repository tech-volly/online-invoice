<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block user notification</title>
</head>
<style>
    @import url(http://fonts.cdnfonts.com/css/circular-std);
    @import url(https://fonts.googleapis.com/icon?family=Material+Icons);
    body {  
        font-family: 'Circular Std', sans-serif;
        background: #f1f1f1;
    }
    .email-section {
        background: #fff;
        box-shadow: 0 2px 4px 0 rgb(136 144 195 / 20%), 0 5px 15px 0 rgb(37 44 97 / 15%);
        border-radius: 15px;
        max-width: 500px;
        padding: 20px 30px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50% );
        overflow: hidden;
        width: 80%;
    }
    .email-section:before {
        content: "";
        position: absolute;
        background: rgb(85 108 214 / 5%);
        width: 300px;
        height: 508px;
        z-index: -1;
        transform: rotate(40deg);
        right: -56px;
        top: -23px;
        border-radius: 35px;
    }
    .email-section .ems-images {
        text-align: center;
    }
    .email-section .ems-images img {
        width: 100px;
        
    }
    .email-section .ems-content p {
        font-size: 16px;
        font-weight: 500;
        line-height: 22px;
        margin-top: 6px;
        text-align: left;
    }
    .email-section .ems-button {
        text-align: center;
    }
    .email-section .ems-button a {
        text-decoration: none;
        color: #fff;
        border-radius: 5px;
        font-weight: 500;
        display: inline-block;
        font-weight: 400;
        line-height: 22px;
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        background: #44b994;
        padding: 10px 30px;
        font-weight: 500;
        font-size: 16px;
        text-transform: uppercase;
        
    }
    .email-section .ems-button a:hover {
        background: #2d7e65;
    } 
    .email-section .ems-content ul {
        padding-left: 0;
        text-align: left;
        list-style: none;
    }
    .email-section .ems-content ul li {
       line-height: 22px;
    }
</style>

<body>
    <div class="email-section">
        <div class="ems-images">
           <img src="{{ URL::asset('public/assets/img/logo2.png')}}?28062022" alt="logo" />
        </div>
        <div class="ems-content">
            <p>Dear Admin,</p>
            <p>Following user has attempt multiple failed login.</p>
            <ul>
                <li><strong>Email :</strong> {{$details['email']}}</li>
                <li><strong>IP Address :</strong> {{$details['ip_address']}}</li>
                <li><strong>DateTime :</strong> {{getFormatedDateTime($details['date_time'])}}</li>
                <li><strong>Login Attempts :</strong> {{$details['login_attempts']}}</li>
            </ul>
        </div>
    </div>
</body>
</html>
