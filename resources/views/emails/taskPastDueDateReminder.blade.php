<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Late Task Reminder Email</h1>

You have <a href="{{route('main.task.lt')}}">{{ $content['late_tasks'] }}</a>  late assignments.<br>

Please clear them as soon as possible.<br>

<a href="{{route('main.task.lt')}}">Late Tasks</a>
<br><br>
<p>Kind Regards,<br>
PD team </p>

</body>
</html>
