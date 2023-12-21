<!DOCTYPE html>
<html lang='en'>

<?php
require __DIR__ . '/vendor/autoload.php';

include __DIR__ . '/Connection.php';
$connection = new Connection();
require_once("MyGmail.php");
$myGmailClient = new MyGmail($connection->getClient());

if (isset($_GET["messageId"])) {
    $messageId = $_GET['messageId'];
    $messageContent = $myGmailClient->getMessage($messageId);

    $headers = $messageContent->getPayload()->getHeaders();
    foreach ($headers as $header) {
        if ($header->getName() == "Subject") {
            echo '<div>';
            echo '<h1>' . $header->getValue() . '</h1>';
            echo '</div>';
        }
    }

    echo '<div>';
    echo '<p>' . $myGmailClient->decodeMessage($messageContent) . '</p>';
    echo '</div>';

}

// Retrieve the message content based on the provided message ID

// Display the message content

?>

</html>