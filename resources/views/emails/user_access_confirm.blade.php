<!DOCTYPE html>
<html>
<head>
    <title>EXP Account Creation</title>
</head>
<body>
    <p>Dear {{ $details['name'] }},</p>
    <p>We're pleased to confirm that your EXP account has been successfully created.</p>

    <h3>Account Details:</h3>
    <ul>
        <li>Email: {{ $details['email'] }}</li>
        <li>Event: {{ $details['event'] }}</li>
        <li>Venue: {{ $details['venue'] }}</li>
    </ul>

    <p>To get started, please log into your account using the link below:</p>
    <p><a href="https://exp.sc.qa">Log into Your EXP Account</a></p>
    <p>If you have any questions or require assistance, feel free to contact us at exp@sc.qa.</p>
    <p>Best regards,<br>EXP Support Team</p>
</body>
</html>
