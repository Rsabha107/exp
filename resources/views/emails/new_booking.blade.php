<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MDS Confirmation for a New Booking Request: {{ $details['booking_ref_number'] }}</title>
</head>
<body>
<h1>MDS Ref#: {{ $details['booking_ref_number'] }}</h1>

Dear MDS User,<br> 

<p>We are pleased to confirm that your booking request for {{ $details['venue'] }} on {{ $details['booking_date'] }} at {{ time_range_segment($details['booking_time_slot'],'from') }} has been successfully added to the schedule.<br />
Attached is your Booking Pass in PDF format for your reference. Please ensure that the driver adheres to the RSP arrival times and carries a printed copy of the attached pass on the delivery</p>

<p>Kindly note that if any modifications or changes are needed for a confirmed booking (Venue, Driver, or Vehicle), you will need to submit a Modify Booking Request before 17:00 for the next day.</p>

<p>Should you have any further questions, please don't hesitate to contact us.<br />
On behalf of the MDS Team, we appreciate your use of our system and wish you a wonderful day ahead.</p>

<p>Kind Regards,<br>
MDS team </p>

</body>
</html>
