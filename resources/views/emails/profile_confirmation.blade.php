<!DOCTYPE html>
<html>
<head>
    <title>Profile Confirmation</title>
</head>
<body>
    <h2>Hi {{ $profile->first_name }},</h2>
    <p>Your profile has been submitted successfully.</p>

    <p><strong>Refernce Number:</strong> {{ $profile->ref_number }}</p>

    <p>Scan this QR code to verify or access your profile:</p>


    <p>Thank you!</p>
</body>
</html>