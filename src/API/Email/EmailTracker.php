<?php

namespace Drupal\controlpanel\API\Email;

class EmailTracker
{
    protected $to = null;
    protected $from = null;
    protected $messageId = null;
    protected $imageId = null;
    protected $dbConnection = null;

    public function __construct($to = null, $from = null, $messageId = null, $campaignId = 0)
    {
        $this->to = $to;
        $this->from = $from;
        $this->messageId = $messageId;
        $this->campaignId = $campaignId;
        $this->dbConnection = \Drupal::database();
    }

    public function getImageId()
    {
        $this->generateImageId();
        return $this->imageId;
    }

    public function generateImageId()
    {
        $imageInformation = [
            'to' => $this->to,
            'from' => $this->from,
            'messageId' => $this->messageId,
            'campaignId' => $this->campaignId,
            'date' => date('Y-m-d H:i:s')
        ];
        $this->imageId = base64url_encode(json_encode($imageInformation));
    }

    public function decodeImageId()
    {
        if (!empty($this->imageId)) {
            $mailPartJSON = base64url_decode($this->imageId);
            $imageInformation = json_decode($mailPartJSON, 1);
            \Drupal::logger('controlpanel')->notice('Decoded Id - ' . print_r($imageInformation, 1));
            $this->to = $imageInformation['to'];
            $this->from = $imageInformation['from'];
            $this->messageId = $imageInformation['messageId'];
            $this->campaignId = isset($imageInformation['campaignId']) ? $imageInformation['campaignId'] : 0;
            $this->date = isset($imageInformation['date']) ? $imageInformation['date'] : '2020-09-24 12:00:00';
            $this->dbConnection
                ->insert('email_view_log')
                ->fields([
                    'message_id' => $this->messageId,
                    'to_user' => $this->to,
                    'from_user' => $this->from,
                    'ip' => get_client_ip(),
                    'referrer' => json_encode([])
                ])
                ->execute();
        }
    }

    public function setImageId($image)
    {
        $imageParts = pathinfo($image);
        if ($imageParts['extension'] == 'png') {
            $this->imageId = $imageParts['filename'];
            $this->decodeImageId();
        }
    }

    public function serveImage()
    {
        // header('Content-Type: image/png');
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
    }
}