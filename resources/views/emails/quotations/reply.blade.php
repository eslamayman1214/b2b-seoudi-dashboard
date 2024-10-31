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
            text-align: center;
            /* Center-aligns all content within the container */
        }

        .header {
            margin-bottom: 20px;
        }

        .header img {
            max-width: 100px;
            /* Adjusts logo size */
            width: 20%;
            height: auto;
            margin-bottom: 0px;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            text-align: left;
            /* Left-aligns the content text */
        }

        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
            text-align: center;
        }

        .status-approved {
            font-weight: bold;
            color: #28a745;
        }

        .status-rejected {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            <img src="https://i.postimg.cc/KzkLzm46/Seoudi-Logo.jpg" alt="Seoudi Supermarket Logo"
                style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
            Quotation Status Update
        </div>

        <div class="content">
            <p>Hello {{ $quotation->name }},</p>
            <p>We wanted to inform you that your quotation request has been
                <span class="{{ $status === 'approved' ? 'status-approved' : 'status-rejected' }}">
                    {{ ucfirst($status) }}
                </span>.
            </p>

            <p><strong>Quantity Required:</strong> {{ $quotation->quantity_required }}</p>
            <p><strong>Description:</strong> {{ $quotation->description }}</p>

            @if ($notes)
                <p><strong>Notes:</strong> {{ $notes }}</p>
            @endif

            <p>Thank you for your interest in working with us. Please feel free to reach out if you have further
                questions.</p>
        </div>
        <div class="footer">
            Best Regards,<br>
            Seoudi Supermarket Team
        </div>
    </div>
</body>

</html>
