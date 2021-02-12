<?php

namespace Drupal\controlpanel\API\Email;

class EmailLog {
    protected $username;
    protected $dbConnection;

    public function __construct($username){
        $this->username = $username;
        $this->dbConnection = \Drupal::database();
    }

    public function setLog(){
        $transaction = $this->dbConnection->startTransaction();
        try{
            $log = $this->dbConnection->query(
                'select * from mx_accounts_log where username = :username and date = :date limit 1',
                [':username'=> $this->username, 'date' => date('Y-m-d')],
            )->fetch();
            \Drupal::messenger()->addMessage(print_r($log,1));
            \Drupal::logger('conrolpanel')->notice(print_r($log,1));
            $mail_count = 0;
            if(!empty($log) && !empty($log->username)){
                $mail_count = $log->mail_count;
            }
            $mail_count++;
            $this->dbConnection
                ->merge('mx_accounts_log')
                ->keys(['username'=>$this->username, 'date'=> date('Y-m-d')])
                ->fields(['mail_count'=>$mail_count,'last_mail_sent'=>date("Y-m-d H:i:s")])
                ->execute();
        } catch (\Exception $e) {
            \Drupal::messenger()->addMessage($e->getMessage());
            $transaction->rollBack();
        }
    }

    public function setInactive(){
        $this->dbConnection
                ->merge('mx_accounts')
                ->keys(['username'=>$this->username])
                ->fields(['active' => 0])
                ->execute();
    }

    public function genericLog($fromEmail, $message){
        $this->dbConnection
            ->insert('mx_accounts_error_log')
            ->fields(
                [
                    'from_email'=>$fromEmail,
                    'username'=> $this->username,
                    'error_message'=>$message
                ]
            )->execute();
    }
}