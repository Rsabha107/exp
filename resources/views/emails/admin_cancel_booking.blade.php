<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MDS Deletion of a Confirmed Slot by the administrator ({{ $details['booking_ref_number'] }})</title>
</head>
<body>
<h1>MDS Ref#: {{ $details['booking_ref_number'] }}</h1>

Dear MDS User,<br> 

<p>We regret to inform you that due to other urgent deliveries, we are unable to accommodate your booking for {{ $details['venue'] }} on {{ $details['booking_date'] }} at {{ time_range_segment($details['booking_time_slot'],'from') }}, as no slots are currently available.</p>

<p>Please visit our MDS portal to select a new available slot that best fits your delivery schedule.</p>

<p>Should you have any further questions, please don't hesitate to contact us.<br />
On behalf of the MDS Team, we appreciate your use of our system and wish you a wonderful day ahead.</p>

<p>Kind Regards,<br>
MDS team </p>

</body>
</html>
