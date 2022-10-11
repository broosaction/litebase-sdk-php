<?php

namespace Litebase\Service\AriaStudio;

use Litebase\Common\Http\Exception;
use Litebase\LitebaseClient;
use Litebase\LitebaseService;

class Registration extends LitebaseService
{

    public function __construct(LitebaseClient $client)
    {
        parent::__construct($client);
        $this->endpoint = '/api/studio/checkreg';
        $this->rootUrl = LitebaseClient::API_BASE_PATH;
        $this->version = 'v1';
        $this->oldData = array();
    }




    public function createAccount($email, $fullname, $pass){

        $this->oldData['action'] = 'add_account';
        $this->oldData['email'] = $email;
        $this->oldData['fullname'] = $fullname;
        $this->oldData['pass'] = $pass;

        //check the task run
        if (!$this->hasExected) {
            $this->_execute();
        }

        return $this->newData->data;
    }


    public function createproject($username, $name, $link){


        $this->oldData['action'] = 'add_project';
        $this->oldData['user'] = explode('_',$username)[0];
        $this->oldData['name'] = $name;
        $this->oldData['link'] = $link;

        //check the task run

            $this->_execute();


        return $this->newData->data;
    }


    public function getUsername($email){
        $this->oldData['action'] = 'get_username';
        $this->oldData['email'] = $email;
        if (!$this->hasExected) {
            $this->_execute();
        }

        return $this->newData->username;
    }

    public function getAPIkeys($username, $link){
        $this->oldData['action'] = 'get_apikeys';
        $this->oldData['id'] = explode('_',$username)[0];
        $this->oldData['link'] = $link;
        if (!$this->hasExected) {
            $this->_execute();
        }

        return $this->newData->data;
    }


    /**
     *
     * @return mixed
     * @throws \JsonException
     */
    public function isLinkTaken($text)
    {
        $this->oldData['action'] = 'check_link';
        $this->oldData['search'] = $text;


        if (!$this->hasExected) {
            $this->_execute();

        }

        return $this->newData->data === 'yes';

    }

    /**
     * executes our request
     * @throws Exception
     */
    private function _execute(): void
    {
        if (isset($this->oldData['action'])) {
            $this->hasExected = false;
            $this->execute($this->oldData);

        } else {
            throw new Exception('link not set.');
        }

    }



}