<?php 
use Google\Service\Gmail\Message;

class MessagePage {
    /** @var Message[] */ 
    private  $messages;
    private $nextPageToken;

    /**
     * @param Message[] $messages
     */
    public function __construct($messages, $nextPageToken) {
        $this->messages = $messages;
        $this->nextPageToken = $nextPageToken;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function getNextPageToken() {
        return $this->nextPageToken;
    }
}