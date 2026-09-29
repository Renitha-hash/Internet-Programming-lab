<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$responseMessage = "";
$isSuccess = false;
$submittedData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Retrieve and sanitize input data
    $firstName   = trim($_POST['firstName'] ?? '');
    $lastName    = trim($_POST['lastName'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $rawPassword = $_POST['password'] ?? '';
    
    // Credit card fields
    $cardNumber  = preg_replace('/\s+/', '', $_POST['cardNumber'] ?? '');
    $cardExpiry  = trim($_POST['cardExpiry'] ?? '');
    $cardCvv     = trim($_POST['cardCvv'] ?? '');

    $errors = [];

    // 2. Server-side validation checks
    if (empty($firstName)) $errors[] = "First name is required.";
    if (empty($lastName)) $errors[] = "Last name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!preg_match('/^\d{10}$/', $phone)) $errors[] = "10-digit phone number is required.";
    if (strlen($rawPassword) < 6) $errors[] = "Password must be at least 6 characters.";

    // Server-side Credit Card validation
    if (!preg_match('/^\d{13,19}$/', $cardNumber)) {
        $errors[] = "Invalid credit card number format.";
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $cardExpiry)) {
        $errors[] = "Invalid expiry format (use MM/YY).";
    }
    if (!preg_match('/^\d{3,4}$/', $cardCvv)) {
        $errors[] = "Invalid CVV code.";
    }

    // 3. Save data directly to local JSON file
    if (empty($errors)) {
        $dataFile = 'users.json';
        
        $users = [];
        if (file_exists($dataFile)) {
            $currentData = file_get_contents($dataFile);
            $users = json_decode($currentData, true) ?? [];
        }

        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $errors[] = "Error: This email is already registered.";
                break;
            }
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
            $maskedCard = '****-****-****-' . substr($cardNumber, -4);

            $newUser = [
                'id'           => uniqid(),
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'email'        => $email,
                'phone'        => $phone,
                'password'     => $hashedPassword,
                'card_masked'  => $maskedCard,
                'card_expiry'  => $cardExpiry,
                'created_at'   => date('Y-m-d H:i:s')
            ];

            $users[] = $newUser;
            if (file_put_contents($dataFile, json_encode($users, JSON_PRETTY_PRINT))) {
                $isSuccess = true;
                $responseMessage = "Registration successful!";
                $submittedData = $newUser;
            } else {
                $responseMessage = "Error: Could not write file data.";
            }
        } else {
            $responseMessage = implode("<br>", $errors);
        }
    } else {
        $responseMessage = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Result</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
    .card { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 480px; margin: 20px; }
    h2 { color: <?php echo $isSuccess ? '#28a745' : '#dc3545'; ?>; text-align: center; }
    p { color: #333; line-height: 1.5; text-align: center; }
    .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
    .details-table th, .details-table td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; }
    .details-table th { background-color: #f8f9fa; color: #333; width: 35%; }
    .details-table td { color: #555; }
    .btn-back { display: block; text-align: center; margin-top: 20px; color: #fff; background: #0d6efd; padding: 10px; text-decoration: none; border-radius: 5px; }
    .btn-back:hover { background: #0b5ed7; }
  </style>
</head>
<body>
  <div class="card">
    <h2><?php echo $isSuccess ? 'Registration Successful!' : 'Registration Failed'; ?></h2>
    <p><?php echo $responseMessage; ?></p>

    <?php if ($isSuccess): ?>
      <h3 style="font-size: 16px; margin-top: 20px; color: #444; border-bottom: 2px solid #0d6efd; padding-bottom: 5px;">Submitted Details Summary</h3>
      <table class="details-table">
        <tr>
          <th>Full Name</th>
          <td><?php echo htmlspecialchars($submittedData['first_name'] . ' ' . $submittedData['last_name']); ?></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><?php echo htmlspecialchars($submittedData['email']); ?></td>
        </tr>
        <tr>
          <th>Phone</th>
          <td><?php echo htmlspecialchars($submittedData['phone']); ?></td>
        </tr>
        <tr>
          <th>Card Charged</th>
          <td><?php echo htmlspecialchars($submittedData['card_masked']); ?> (Exp: <?php echo htmlspecialchars($submittedData['card_expiry']); ?>)</td>
        </tr>
      </table>
    <?php endif; ?>

    <a href="index.html" class="btn-back">Go Back to Form</a>
  </div>
</body>
</html>