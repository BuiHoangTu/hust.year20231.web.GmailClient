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
            $gmail = new MyGmail($connection->getClient());

            $pageToken = isset($_GET["pageToken"]) ? $_GET["pageToken"] : NULL;

            $page = $gmail->getMessagePage($pageToken);
            
            // Display the "Next Page" link
            if (!empty($page->getNextPageToken())) {
                echo '<div style="margin-top: 20px; font-weight: bold; font-size: 18px;">';
                echo '<a href="index.php?pageToken=' . $page->getNextPageToken() . '">Next Page</a>';
                echo '</div>';
            }
            
            foreach ($page->getMessages() as $email) {
                echo "<p>";
                echo "Email Id: " . $email->getId();

                $headers = $gmail->getMessage($email->getId())->getPayload()->getHeaders();
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


$main = new Main();
echo "<!DOCTYPE html><html lang='en'>";
echo "<h1>Tu's Gmail</h1>";
$main->main();
echo "</html>";