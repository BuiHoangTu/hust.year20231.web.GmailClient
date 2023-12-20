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
}





// [END gmail_quickstart]
