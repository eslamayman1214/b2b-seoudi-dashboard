<!DOCTYPE html>
<html>

<head>
    <title>New Ticket Assignment</title>
</head>

<body>
    <h1>Hello {{ $user->name }},</h1>
    <p>You have been assigned a new ticket with the following details:</p>

    <p><strong>Ticket ID:</strong> {{ $ticket->id }}</p>
    <p><strong>Description:</strong> {{ $ticket->description }}</p>
    <p><strong>Status:</strong> {{ $ticket->status }}</p>

    <p>Please check the ticket management system to view more details and take necessary action.</p>

    <p>Best regards,<br>Seoudi Support Team</p>
</body>

</html>
