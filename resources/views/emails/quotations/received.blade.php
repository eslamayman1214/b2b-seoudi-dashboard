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
        }

        .content p {
            margin: 8px 0;
        }

        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
            text-align: center;
        }

        .detail-item {
            margin: 8px 0;
            font-weight: bold;
        }

        .detail-value {
            font-weight: normal;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://i.postimg.cc/KzkLzm46/Seoudi-Logo.jpg" alt="Seoudi Supermarket Logo"
                style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
            Quotation Received
        </div>
        <div class="content">
            <p>Hello {{ $quotation->name }},</p>
            <p>Thank you for reaching out to us with your quotation request! We’ve received the following details and
                will be in touch soon:</p>
            <p><span class="detail-item">Name:</span> <span class="detail-value">{{ $quotation->name }}</span></p>
            <p><span class="detail-item">Email:</span> <span class="detail-value">{{ $quotation->email }}</span></p>
            <p><span class="detail-item">Phone:</span> <span
                    class="detail-value">{{ $quotation->phone ?? 'N/A' }}</span></p>
            <p><span class="detail-item">Company:</span> <span
                    class="detail-value">{{ $quotation->company_name ?? 'N/A' }}</span></p>
            <p><span class="detail-item">Quantity Required:</span> <span
                    class="detail-value">{{ $quotation->quantity_required }}</span></p>
            <p><span class="detail-item">Desired Delivery Date:</span> <span
                    class="detail-value">{{ $quotation->desired_delivery_date ?? 'N/A' }}</span></p>
            <p><span class="detail-item">Description:</span> <span
                    class="detail-value">{{ $quotation->description }}</span></p>
            <p><span class="detail-item">Additional Notes:</span> <span
                    class="detail-value">{{ $quotation->additional_notes ?? 'N/A' }}</span></p>
            <p>We’re reviewing your request, and one of our representatives will reach out shortly with a detailed
                response.</p>
            <p>Thank you for choosing us!</p>
        </div>
        <div class="footer">
            Best Regards,<br>
            The Seoudi Supermarket Team
        </div>
    </div>
</body>

</html>
