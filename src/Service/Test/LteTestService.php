<?php
/**
 * Created by Bruce Mubangwa on 07 /Nov, 2020 @ 15:05
 */

namespace Litebase\Service\Test;

use Litebase\LitebaseClient;
use Litebase\LitebaseService;

class LteTestService extends LitebaseService
{

    /**
     * LteTestService constructor.
     * @param LitebaseClient $client
     */
    public function __construct(LitebaseClient $client)
    {
        parent::__construct($client);
        $this->endpoint = '/api/charts/qr';
        $this->rootUrl = 'http://brooshost:8081/cloud/';
        $this->version = 'v1';

    }

    public function getMessage(){
       return $this->execute(array('text'=>'Hello'));
    }


}