<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
</head>
<body>
<table align="center" width="600" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            <table align="center" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background-color: #f0f0f0; padding: 20px;">
                <tr>
                    <td>
                        <img src="{{ logo_url }}" alt=""></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table align="center" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background-color: #ffffff; padding: 20px;">
                <tr>
                    <td>
                        {{{ body }}} <!-- This is the placeholder for email content -->
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
