<?php
class Main {
    public function __construct() {
        // Include others 
        require __DIR__ . '/vendor/autoload.php';
        include __DIR__ .'/Connection.php';
    }

    public function main() {
        $connection = new Connection();

        if ($connection->isConnected()) {
            require_once("MyGmail.php");
            $gmail = new MyGmail($connection->getClient());
            $page = $gmail->getMessagePage(null);

            foreach ($page->getMessages() as $email) {
                echo "<p>";
                echo "Email Id: " . $email->getId();
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