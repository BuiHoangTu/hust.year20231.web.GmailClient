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
    $attachments = $_FILES['attachments'];


    // Send the email
    $gmail->sendMessage($to, $subject, $body, $attachments); // Adjust this based on your implementation

    // Optionally, redirect the user to the index page after sending the email
    header("Location: index.php");
    exit(0);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create New Mail</title>
    <style>
        .attachment-container {
            margin-top: 10px;
        }
    </style>
    <script>
        function addAttachmentInput() {
            var container = document.getElementById('attachment-container');
            var newInput = document.createElement('input');
            newInput.type = 'file';
            newInput.name = 'attachments[]';
            container.appendChild(newInput);
        }
    </script>
</head>
<body>
    <h1>Create New Mail</h1>

    <form method="post" action="" enctype="multipart/form-data">
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

        <!-- File input for initial attachment -->
        <label for="attachments">Attachments:</label>
        <input type="file" id="attachments" name="attachments[]" multiple>
        <br>

        <!-- Container for dynamically added attachments -->
        <div id="attachment-container" class="attachment-container"></div>
        
        <!-- Button to add more attachments -->
        <button type="button" onclick="addAttachmentInput()">Add Attachment</button>
        <br>

        <button type="submit">Send Mail</button>
    </form>
</body>
</html>