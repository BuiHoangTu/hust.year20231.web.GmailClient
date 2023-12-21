<?php
use Google\Service\Gmail;
// Replace With below if future error 
// use Google_Service_Gmail;

class MyGmail
{
    private $service;
    public function __construct($client)
    {
        $this->service = new Gmail($client);
        require_once __DIR__ ."/MessagePage.php";
    }

    public function getLabels()
    {
        $service = $this->service;

        try {
            // Print the labels in the user's account.
            $user = 'me';
            $results = $service->users_labels->listUsersLabels($user);


            $returnedHtml = '';
            if (count($results->getLabels()) == 0) {
                $returnedHtml = "<p>No labels found.</p>";
            } else {
                $returnedHtml = "<p>Labels:<p>";
                foreach ($results->getLabels() as $label) {
                    $returnedHtml .= "<p>";
                    $returnedHtml .= $label->getName();
                    $returnedHtml .= "</p>";
                }
            }

            return $returnedHtml;
        } catch (Exception $e) {
            // TODO(developer) - handle error appropriately
            return 'Message: ' . $e->getMessage();
        }
    }

    public function getMessagePage($pageToken)
    {
        $service = $this->service;
        $userId = "me";

        $optParams = array();

        try {
            if ($pageToken) {
                $optParams["pageToken"] = $pageToken;
            }
            $messagesResponse = $service->users_messages->listUsersMessages($userId, $optParams);

            $messages = $messagesResponse->getMessages();
            return new MessagePage($messages, $messagesResponse->getNextPageToken());
        } catch (Exception $e) {
            print "Error: " . $e->getMessage();
        }
    }

    public function getMessage($messageId)
    {
        $service = $this->service;
        $userId = "me";

        $message = $service->users_messages->get($userId, $messageId);

        return $message;
    }

    public function decodeMessage($message) {
        $service = $this->service;

        $messageInParts = $message->getPayload()->getParts();
        if ($messageInParts != NULL && count($messageInParts) > 1) {
            $data = $messageInParts[1]->getBody()->getData();
        } else {
            $data = $message->getPayload()->getBody()->getData();
        }

        // decode
        $out = str_replace("-", "+", $data);
        $out = str_replace("_", "/", $out);
        return base64_decode($out);
    }

    public function sendMessage($to, $subject, $message) {
        $service = $this->service;
        $from = $service->users->getProfile("me")->getEmailAddress();

        $raw = "From: myAddress<$from>\r\n";
        $raw .= "To: toAddress<$to>\r\n";
        $raw .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
        $raw .= "MIME-Version: 1.0\r\n";
        $raw .= "Content-Type: text/html; charset=utf-8\r\n";
        $raw .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n";
        $raw .= "\r\n$message\r\n";

        $encodedRaw = rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
        $msg = new Gmail\Message();
        $msg->setRaw($encodedRaw);


        $this->service->users_messages->send("me", $msg);
    }
}
