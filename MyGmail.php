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
        require_once __DIR__ . "/MessagePage.php";
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

    public function decodeMessage($message)
    {
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

    public function sendMessage($to, $subject, $message, $attachments)
    {
        $service = $this->service;
        $from = $service->users->getProfile("me")->getEmailAddress();
        $haveAttachments = $attachments != NULL && count($attachments) > 0;

        $raw = "From: myAddress<$from>\r\n";
        $raw .= "To: toAddress<$to>\r\n";
        $raw .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
        $raw .= "MIME-Version: 1.0\r\n";


        if ($haveAttachments) {
            $boundary = uniqid(rand(), true);
            $raw .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
            // message part 
            $raw .= "\r\n--{$boundary}\r\n";
        }

        // with or without attachments
        $raw .= "Content-Type: text/html; charset=utf-8\r\n";
        $raw .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n";
        $raw .= "\r\n$message\r\n";



        if ($haveAttachments) {
            // wrap message part if have attachments 
            $raw .= "--{$boundary}\r\n";

            // attachments
            for ($i = 0; $i < count($attachments['name']); $i++) {
                $mimeType = $attachments['type'][$i];
                $fileName = $attachments['name'][$i];
                $filesize = $attachments['size'][$i];
                $filePath = $attachments['tmp_name'][$i];

                $raw .= "\r\n--{$boundary}\r\n";
                $raw .= 'Content-Type: ' . $mimeType . '; name="' . $fileName . '";' . "\r\n";
                $raw .= 'Content-ID: <' . $from . '>' . "\r\n";
                $raw .= 'Content-Description: ' . $fileName . ';' . "\r\n";
                $raw .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . $filesize . ';' . "\r\n";
                $raw .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
                $raw .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\n") . "\r\n";
                $raw .= "--{$boundary}\r\n";
            }
        }

        // encode 
        $encodedRaw = rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
        $msg = new Gmail\Message();
        $msg->setRaw($encodedRaw);

        // send 
        $this->service->users_messages->send("me", $msg);
    }
}
