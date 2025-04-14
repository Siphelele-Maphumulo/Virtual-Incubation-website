<?php
session_start();

// Verify reCAPTCHA first
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if reCAPTCHA response exists
    if (empty($_POST['g-recaptcha-response'])) {
        header('Content-Type: application/json');
        die(json_encode(['success' => false, 'message' => 'Please complete the CAPTCHA verification']));
    }

    $recaptcha_secret = '6LdVNAMrAAAAALZeVk6P-ISmJd6BIx22InK-Izks';
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $remote_ip = $_SERVER['REMOTE_ADDR'];

    // Verify with Google's reCAPTCHA API
    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $remote_ip
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];

    $context = stream_context_create($options);
    $recaptcha = file_get_contents($recaptcha_url, false, $context);
    $recaptcha = json_decode($recaptcha);

    if (!$recaptcha->success) {
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => 'CAPTCHA verification failed',
            'errors' => $recaptcha->{'error-codes'}
        ]));
    }

    // Rate limiting - session-based
    $rateLimitKey = 'rate_limit_' . $remote_ip;
    $submissionCount = $_SESSION[$rateLimitKey] ?? 0;

    if ($submissionCount >= 5) {
        header('Content-Type: application/json');
        http_response_code(429);
        die(json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']));
    }

    // Process form data
    $name = strip_tags(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    
    // Validate inputs
    if (empty($name) || empty($email)) {
        header('Content-Type: application/json');
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Name and email are required']));
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Invalid email format']));
    }

    // Email configuration and sending
    $to = "siphelelemaphumulo@gmail.com";
    $subject = "SMMEs Virtual Incubation - New Subscriber: " . substr($name, 0, 50);
    
    $message = '
    <html>
    <head>
        <title>New Subscriber</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .header { color: #0066cc; font-size: 18px; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .footer { margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2 class="header">SMMEs Virtual Incubation - New Subscription</h2>
            <p><strong>Name:</strong> '.htmlspecialchars($name, ENT_QUOTES).'</p>
            <p><strong>Email:</strong> '.htmlspecialchars($email, ENT_QUOTES).'</p>
            <p><strong>Date:</strong> '.date('Y-m-d H:i:s').'</p>
            <div class="footer">
                <p>This email was generated automatically. Please do not reply.</p>
            </div>
        </div>
    </body>
    </html>';

    $headers = [
        'From' => mb_encode_mimeheader($name) . " <siphelelemaphumulo@gmail.com>",
        'Reply-To' => $email,
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=UTF-8',
        'X-Mailer' => 'PHP/' . phpversion()
    ];
    
    $headerString = '';
    foreach ($headers as $key => $value) {
        $headerString .= "$key: $value\r\n";
    }

    // Send email
    if (mail($to, $subject, $message, $headerString)) {
        $_SESSION[$rateLimitKey] = $submissionCount + 1;
        header('Content-Type: application/json');
        die(json_encode(['success' => true, 'message' => 'Thank You! You have been subscribed.']));
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Oops! Something went wrong. Please try again later.']));
    }
} else {
    header('Content-Type: application/json');
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}
?>