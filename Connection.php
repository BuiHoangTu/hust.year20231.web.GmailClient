<?php
use Google\Client;


class Connection
{
    private $REDIRECT_URL = "http://localhost/20200547/";
    private $credentials;
    private $client;
    private $connected = false;

    public function __construct()
    {
        $this->credentials = "credentials-web.json";
        $this->client = $this->createClient();
        $this->client->setRedirectUri($this->REDIRECT_URL);
    }

    private function createClient()
    {
        $client = new Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->setScopes('https://mail.google.com/');
        $client->setAuthConfig($this->credentials);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = 'token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }
        
        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else if ($this->credentialsInBrowser()) {
                $authCode = $_GET['code'];

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                if ($accessToken['error']) {
                    error_log("$accessToken[error]: $accessToken[error_description]");
                    $this->connected = false;
                    return $client;
                }
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            } else {
                // None have credentials
                $this->connected = false;
                return $client;
            }

            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            echo "<p> Token is already OK </p>";
        }

        $this->connected = true;
        return $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function isConnected()
    {
        return $this->connected;
    }

    public function getUnauthData()
    {
        $authUrl = $this->client->createAuthUrl();
        return "<a href='$authUrl'> Click here to link your accounts </a>";
    }

    public function credentialsInBrowser()
    {
        if ($_GET["code"]) {
            return true;
        }
        return false;
    }
}
