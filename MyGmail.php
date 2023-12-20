<?php
use Google\Service\Gmail;
// Replace With below if future error 
use Google_Service_Gmail;

class MyGmail
{
    private $client;
    public function __construct($client)
    {
        $this->client = $client;
    }

    public function getLabels()
    {
        $service = new Gmail($this->client);

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

    public function getMessages()
    {
        $service = new Gmail($this->client);
        $userId = "me";

        $pageToken = NULL;
        $messages = array();
        $optParams = array();

        $i = 0;
        do {
            if ($i > 5)
                break;
            $i++;
            try {
                if ($pageToken) {
                    $optParams["pageToken"] = $pageToken;
                }
                $messagesResponse = $service->users_messages->listUsersMessages($userId, $optParams);

                if ($messagesResponse->getMessages()) {
                    $messages = array_merge($messages, $messagesResponse->getMessages());
                    $pageToken = $messagesResponse->getNextPageToken();
                }
            } catch (Exception $e) {
                print "Error: " . $e->getMessage();
            }
        } while ($pageToken);

        $ii = 0;
        foreach ($messages as $m) {
            print "MessageId: " . $m->getId() . "<br/>";
            $message = $service->users_messages->get($userId, $m->getId());

            $messageInParts = $message->getPayload()->getParts();
            if ($messageInParts != NULL && count($messageInParts) > 1) {
                $data = $messageInParts[1]->getBody()->getData();
            } else {
                $data = $message->getPayload()->getBody()->getData();
            }

            echo "<pre>".var_export($data, true)."</pre>";  

            if ($ii++ >= 10) break;
        }

        return $messages;

    }
}
