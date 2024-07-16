<!-- resources/views/emails/otp.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
</head>

<body>
    <p>Dear <b>{{ $name }}</b>,</p>
    <p>Use the following code to complete your login:</p>
    <p><strong>{{ $otp }}</strong></p>
    <p>Note: this code is valid for 2 minutes only.</p>
    <p>Thank you!</p>
    <p>Seoudi development team.</p>
</body>

</html>
