<?php
/**
 * Created by Bruce Mubangwa on 07 /Nov, 2020 @ 11:03
 */

namespace Litebase;

use Litebase\Common\Crypt;
use Litebase\Common\Http\Method;
use Litebase\Common\Http\Request;
use Litebase\Common\Http\Request\Body;
use Litebase\Common\Http\Response;

class LitebaseService
{

    protected $endpoint;
    protected $version;
    protected $rootUrl;
    protected $servicePath;
    protected $availableScopes;
    protected $serviceName;
    protected $resource;
    protected $client;
    private $data;

    public function __construct(LitebaseClient $client)
    {
        $this->client = $client;
    }

    /**
     * Return the associated Client class.
     * @return LitebaseClient
     */
    protected function getClient()
    {
        return $this->client;
    }


    private function do($type = Method::POST): Response
    {
        // make request to endpoint

        $bearerTkn = $this->client->getClient_id();
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => $bearerTkn,
            'user-agent' => $this->client::USER_AGENT,
            'username' => $this->client->getConfig()['username'],
        );


        // the
        //$body = $this->data;
        $body = Body::Json($this->data);

        $this->rootUrl = $this->rootUrl ?? $this->client->getConfig()['base_url'];
        $url = $this->rootUrl . $this->endpoint;

        if ($type === Method::POST) {
            $response = Request::post($url, $headers, $body);
        } else if ($type === Method::PUT) {
            $response = Request::put($url, $headers, $body);
        } else if ($type === Method::DELETE) {
            $response = Request::delete($url, $headers, $body);
        } else if ($type === Method::GET) {
            $response = Request::get($url, $headers, $body);
        } else {
            //sometimes things get complicated and funny
            $response = Request::post($url, $headers, $body);
        }

        return $response;
    }

    /**
     * @param array $options
     * @param bool $encrypt
     * @param string $requestType
     * @return Response
     */
    protected function execute(array $options, $encrypt = true, $requestType = Method::POST): Response
    {

        if ($encrypt) {
            //encrypt the required options to pass to the server
            $crypt = new Crypt($this->client);
            $this->integrityHash = $crypt->encryption($options);

            $this->data = array(
                'data' => $this->integrityHash,
                'encrypted' => 1,
                'target_api' => $this->client->getTarget_api(),
                'application_name' => $this->client->getApplication_name(),
            );

        } else {

            $this->data = array(
                'data' => $options,
                'encrypted' => 0,
                'target_api' => $this->client->getTarget_api(),
                'application_name' => $this->client->getApplication_name(),
            );

        }


        // the result returned requires validation
        return $this->do($requestType);

    }


}