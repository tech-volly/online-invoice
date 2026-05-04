<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Reminder</title>
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
                                    <img src="{{ URL::asset('public/assets/img/logo2.png') }}" alt="logo" style="width: 100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="left">
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 15px;">Dear {{ $details['userName'] }},</p>
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 15px;"> This is a reminder for invoice #{{ $details['invoice_number'] }}.</
                    </p>
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 15px;">The invoice was due on {{ date('d F, Y', strtotime($details['invoice_due_date'])) }} Can you please process the payment at your earliest convenience?</p>
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 15px;">Please review the attached invoice and let us know if you have any questions.</p>
                </td>
            </tr>
            <tr>
                <td align="left">
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Invoice No: </strong> #{{ $details['invoice_number'] }}</p>
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Invoice Date: </strong> {{ date('d F, Y', strtotime($details['invoice_date'])) }}</p>
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Due Date: </strong> {{ date('d F, Y', strtotime($details['invoice_due_date'])) }}</p>
                    <p style="font-size: 16px;font-weight: 500; line-height: 22px; margin-top: 5px; margin-bottom: 5px;"><strong>Invoice Total: </strong> {{ $details['invoice_total_formatted'] }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>