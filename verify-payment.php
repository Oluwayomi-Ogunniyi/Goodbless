<?php
// verify-payment.php - Paystack Webhook for Godbless Vikky

header('Content-Type: application/json');

// Read the raw POST data from Paystack
$input = @file_get_contents("php://input");

if (!$input) {
    http_response_code(400);
    exit();
}

// Get the signature from the header
$signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

// Replace with your actual Paystack SECRET KEY
// Get it from Paystack Dashboard > Settings > API Keys & Webhooks
$secret_key = 'sk_test_66e489212b182c1d3776e08854a5f622017ebad7';  // CHANGE THIS!

// Verify the signature
$computed_hash = hash_hmac('sha512', $input, $secret_key);

if ($computed_hash !== $signature) {
    // Invalid signature - possible tampering
    http_response_code(401);
    exit();
}

// Decode the payload
$event = json_decode($input);

if ($event->event === 'charge.success') {
    $reference = $event->data->reference;
    $amount_paid = $event->data->amount / 100; // Convert from kobo
    $email = $event->data->customer->email;
    $status = $event->data->status;
    $paid_at = $event->data->paid_at;

    // HERE: Add your own actions when payment is successful
    // Examples:
    // - Save order to a database
    // - Send confirmation email
    // - Update stock
    // - Log to a file

    // Simple example: Log successful payment to a file
    $log = "SUCCESS | Ref: $reference | Email: $email | Amount: ₦$amount_paid | Time: $paid_at\n";
    file_put_contents('payments.log', $log, FILE_APPEND | LOCK_EX);

    // You can also send email here using mail() or PHPMailer
}

// Always respond with 200 to acknowledge receipt
http_response_code(200);
echo json_encode(['status' => 'success']);
?>