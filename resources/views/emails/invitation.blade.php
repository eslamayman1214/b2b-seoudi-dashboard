<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Email</title>
</head>

<body>
    <p>Dear <b>{{ $name }}</b>,</p>
    <p>You have been invited to Seoudi b2b dashboard.</p>
    <p>To login use your offical email address: <b>{{ $email }}</b></p>
    <p> and Your temporary password is: <b>{{ $password }}</b></p>
    <p>Kindly the following link to login and change your password:<a href="{{ route('login') }}"><b>
                <ul>login</ul>
            </b></a></p>
    <p>Thank you!</p>
    <p>Seoudi development team.</p>
</body>

</html>
