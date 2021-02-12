<?php

namespace Drupal\controlpanel\Controller;

use Drupal\Core\Controller\ControllerBase;

class CPController extends ControllerBase {
    
    protected $dbConnection;
    private $logId;

    public function __construct(){
        $this->dbConnection = \Drupal::database();
        $this->setLogId(static::class);
    }

    public function setLogId($logId){
        $this->logId = $logId;
    }

    public function setLog($message, $level = 'notice'){
        \Drupal::logger($this->logId)->log($level, $message);
    }
}