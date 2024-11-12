<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .header {
            margin-bottom: 20px;
            text-align: center;
        }

        .header img {
            max-width: 100px;
            width: 20%;
            height: auto;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://i.postimg.cc/KzkLzm46/Seoudi-Logo.jpg" alt="Seoudi Supermarket Logo">
            <h2>Document Rejection Notice</h2>
        </div>
        <div class="content">
            <p>Hello {{ $customerName }},</p>
            <p>We regret to inform you that your document has been rejected for the following reason:</p>
            <p><strong>Rejection Reason:</strong> {{ $rejectionReason }}</p>

            @if ($note)
                <p><strong>Additional Notes:</strong> {{ $note }}</p>
            @endif

            <p>Please address the noted concerns and resubmit your document if needed.</p>
            <p>Thank you for your understanding.</p>
        </div>
        <div class="footer">
            Best Regards,<br>
            The Seoudi Supermarket Team
        </div>
    </div>
</body>

</html>
