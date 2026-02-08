<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (empty($data['name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit();
}

// Ensure at least email or phone is provided
if (empty($data['email']) && empty($data['phone'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email or phone is required']);
    exit();
}

// Validate GDPR consent
if (empty($data['consent']) || $data['consent'] !== 'true') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'GDPR consent is required']);
    exit();
}

// Sanitize inputs
$name = htmlspecialchars(strip_tags($data['name']));
$businessName = !empty($data['businessName']) ? htmlspecialchars(strip_tags($data['businessName'])) : 'N/A';
$email = !empty($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : 'N/A';
$phone = !empty($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : 'N/A';
$message = htmlspecialchars(strip_tags($data['message']));
$website = !empty($data['website']) ? htmlspecialchars(strip_tags($data['website'])) : 'N/A';
$source = !empty($data['source']) ? htmlspecialchars(strip_tags($data['source'])) : 'Standard Contact Form';

// Validate email format if provided
if ($email !== 'N/A' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Prepare email
$to = 'gdpr@jole.ro';
$subject = 'New Form Submission (' . $source . ') - JOLERO SRL';
$emailBody = "New form submission from: $source\n\n";
$emailBody .= "Name: $name\n";
$emailBody .= "Business Name: $businessName\n";
$emailBody .= "Website: $website\n";
$emailBody .= "Email: $email\n";
$emailBody .= "Phone: $phone\n";
$emailBody .= "Message:\n$message\n\n";
$emailBody .= "---\n";
$emailBody .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
$emailBody .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Email headers
$headers = "From: noreply@jole.ro\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email
$mailSent = mail($to, $subject, $emailBody, $headers);

if ($mailSent) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
?>
