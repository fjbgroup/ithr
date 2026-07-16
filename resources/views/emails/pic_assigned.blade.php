<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Meeting Room PIC Assignment</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f7f6;
            padding: 40px 20px;
        }
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .email-header {
            background-color: #1e3a8a;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .email-body {
            padding: 40px 30px;
            color: #334155;
            line-height: 1.6;
        }
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 24px;
        }
        .message {
            font-size: 16px;
            margin-bottom: 24px;
        }
        .highlight-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            border-radius: 4px;
            margin: 24px 0;
        }
        .room-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e40af;
            margin: 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <h1>Room PIC Assignment</h1>
            </div>
            <div class="email-body">
                <div class="greeting">Hello {{ $userName }},</div>
                <div class="message">
                    You have been assigned as a <strong>Person In Charge (PIC)</strong> for a meeting room in the Nexus System.
                </div>
                <div class="highlight-box">
                    <p style="margin: 0; color: #3b82f6; font-size: 14px; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Assigned Room</p>
                    <p class="room-name">{{ $roomName }}</p>
                </div>
                <div class="message">
                    As the PIC, please ensure the room is well-maintained, equipment is functioning, and it is ready for upcoming bookings. You will also be responsible for approving or rejecting booking requests for this room if approval is required.
                </div>
                <div class="message">
                    Thank you for your cooperation!
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Nexus System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
