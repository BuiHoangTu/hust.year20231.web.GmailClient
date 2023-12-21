<?php
class Main
{
    public function __construct()
    {
        // Include others 
        require __DIR__ . '/vendor/autoload.php';
        include __DIR__ . '/Connection.php';
    }

    public function main()
    {
        $connection = new Connection();

        if ($connection->isConnected()) {
            require_once("MyGmail.php");
            $client = new MyGmail($connection->getClient());

            $pageToken = isset($_GET["pageToken"]) ? $_GET["pageToken"] : NULL;

            $page = $client->getMessagePage($pageToken);

            echo "<div class='vertical-box'>";
            // Create mail 
            echo "<div>";
            echo '<a href="create_mail.php" class="box-item" style="margin-left: 20px;">Create Mail</a>';
            echo '</div>';

            // Display the "Next Page" link
            if (!empty($page->getNextPageToken())) {
                echo '<div class="box-item" style="margin-top: 20px; font-weight: bold; font-size: 18px;">';
                echo '<a href="index.php?pageToken=' . $page->getNextPageToken() . '">Next Page &rarr;</a>';
                echo '</div>';
            }
            echo "</div>";

            foreach ($page->getMessages() as $email) {
                echo "<p>";
                echo "Email Id: " . $email->getId();

                $headers = $client->getMessage($email->getId())->getPayload()->getHeaders();
                foreach ($headers as $header) {
                    if ($header->getName() == "Subject") {
                        echo '<div>';
                        echo '<a href="view_mail.php?messageId=' . $email->getId() . '">' . $header->getValue() . '</a>';
                        echo '</div>';
                    }
                }

                echo "</p>";
            }

        } else {
            echo $connection->getUnauthData();
        }
    }
}
?>


<!DOCTYPE html>
<html lang='en'>

<head>
    <title>Tu's Gmail</title>
    <style>
        .vertical-box {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 300px;
            border: 1px solid #ccc;
        }

        .box-item {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <h1>Tu's Gmail</h1>

    <?php
    $main = new Main();
    $main->main();
    ?>

</body>

</html>