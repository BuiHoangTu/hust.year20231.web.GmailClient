<?php
require __DIR__ . '/vendor/autoload.php';

include __DIR__ . '/Connection.php';
$connection = new Connection();
require_once("MyGmail.php");
$gmail = new MyGmail($connection->getClient());

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have form fields like $to, $subject, $body
    $to = $_POST["to"];
    $subject = $_POST["subject"];
    $body = $_POST["body"];

    // Send the email
    $gmail->sendMessage($to, $subject, $body); // Adjust this based on your implementation

    // Optionally, redirect the user to the index page after sending the email
    header("Location: index.php");
    exit(0);
}

// Display your HTML form for creating a new mail
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create New Mail</title>
</head>
<body>
    <h1>Create New Mail</h1>

    <form method="post" action="">
        <!-- Your form fields go here -->
        <label for="to">To:</label>
        <input type="text" id="to" name="to" required>
        <br>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required>
        <br>

        <label for="body">Body:</label>
        <textarea id="body" name="body" required></textarea>
        <br>

        <button type="submit">Send Mail</button>
    </form>
</body>
</html>