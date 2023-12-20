<?php
class Main {
    public function __construct() {
        // Include others 
        require __DIR__ . '/vendor/autoload.php';
        include __DIR__ .'/connection.php';
    }

    public function main() {
        $connection = new Connection();

        if ($connection->isConnected()) {
            require_once("MyGmail.php");
            $gmail = new MyGmail($connection->getClient());
            $emails = $gmail->getMessages();

            foreach ($emails as $email) {
                echo $email;
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