<?php

namespace Drupal\controlpanel\API\Email;

class EmailLogYahoo {
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
                'select * from mc__yahoo_mx_log where username = :username and date = :date limit 1',
                [':username'=> $this->username , 'date' => date('Y-m-d')],
            )->fetch();
            $mail_count = 0;
            if(!empty($log) && !empty($log->username)){
                $mail_count = $log->mail_count;
            }
            $mail_count++;
            $this->dbConnection
                ->merge('mc__yahoo_mx_log')
                ->keys(['username'=>$this->username, 'date'=> date('Y-m-d')])
                ->fields(['mail_count'=>$mail_count,'last_mail_sent'=>date("Y-m-d H:i:s")])
                ->execute();
        } catch (\Exception $e) {
            \Drupal::messenger()->addMessage($e->getMessage());
            $transaction->rollBack();
        }
    }

    
    public function setTestLog(){
        $transaction = $this->dbConnection->startTransaction();
        try{
            $log = $this->dbConnection->query(
                'select * from mc__yahoo_mx_log where username = :username and date = :date limit 1',
                [':username'=> $this->username , 'date' => date('Y-m-d')],
            )->fetch();
            $mail_count = 0;
            if(!empty($log) && !empty($log->username)){
                $mail_count = $log->test_mail_count;
            }
            $mail_count++;
            $this->dbConnection
                ->merge('mc__yahoo_mx_log')
                ->keys(['username'=> $this->username, 'date'=> date('Y-m-d')])
                ->fields(['test_mail_count'=>$mail_count,'last_mail_sent'=>date("Y-m-d H:i:s")])
                ->execute();
        } catch (\Exception $e) {
            \Drupal::messenger()->addMessage($e->getMessage());
            $transaction->rollBack();
        }
    }

    public function setTestEmailIdUsedLog($tomailid){
        $transaction = $this->dbConnection->startTransaction();
        try{
            $log = $this->dbConnection->query(
                'select * from mc__yahoo_test_account_log where yahoo_username = :yahoo_username 
                and test_email_id = :test_email_id limit 1',
                [':yahoo_username'=> $this->username, 'test_email_id' => $tomailid],
            )->fetch();
            $mail_count = 0;
            if(!empty($log) && !empty($log->username)){
                $mail_count = $log->count;
            }
            $mail_count++;
            $this->dbConnection
                ->merge('mc__yahoo_test_account_log')
                ->keys(['yahoo_username'=>$this->username, 'test_email_id' => $tomailid])
                ->fields(['count'=>$mail_count,'last_mail_sent'=>date("Y-m-d H:i:s")])
                ->execute();
        } catch (\Exception $e) {
            \Drupal::messenger()->addMessage($e->getMessage());
            $transaction->rollBack();
        }
    }

    public function setInactive(){
        $this->dbConnection
        ->merge('mc__yahoo_mx_accounts')
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