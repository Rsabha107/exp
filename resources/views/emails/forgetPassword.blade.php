<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Forget Password Email</h1>

Please use this link to reset your password: <br>

<a href="{{ route('reset.password.get', $token) }}">Reset Password</a>
<br><br>
<p>Kind Regards,<br>
PD team </p>

</body>
</html>
