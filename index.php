<?php
class Main {
    public function __construct() {
        // Include others 
        require __DIR__ . '/vendor/autoload.php';
        include __DIR__ .'/connection.php';
    }

    public function go() {
        $connection = new Connection();

        if ($connection->isConnected()) {
            require_once("MyGmail.php");
            $gmail = new MyGmail($connection->getClient());
            return $gmail->getMessages();
        } else {
            return $connection->getUnauthData();
        }
    }
}


$main = new Main();
echo "<!DOCTYPE html><html lang='en'>";
echo "<h1>Tu's Gmail</h1>";
echo $main->go();
echo "</html>";