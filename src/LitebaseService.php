<?php
/**
 * Created by Bruce Mubangwa on 07 /Nov, 2020 @ 11:03
 */

namespace Litebase;


use Litebase\Common\Crypto\Crypt;
use Litebase\Common\Http\Method;
use Litebase\Common\Http\Request;
use Litebase\Common\Http\Request\Body;
use Litebase\Common\Http\Response;

class LitebaseService
{
    public $hasExected;
    /**
     * @var mixed
     */
    protected array $oldData;

    protected $endpoint;
    protected $version;
    protected $rootUrl;
    protected $encrypt = true;
    protected $servicePath;
    protected $availableScopes;
    protected $serviceName;
    protected $resource;
    protected $client;
    public $newData;

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
            'authorization' => $bearerTkn,
            'user-agent' => $this->client::USER_AGENT,
            'username' => $this->client->getConfig()['username'],
        );

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


        $this->newData = json_decode($response->raw_body, false, 512, JSON_THROW_ON_ERROR);
        $this->hasExected = true;
       
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
        $bearerTkn = $this->client->getClient_id();
        if ($encrypt && $this->encrypt) {

            //encrypt the required options to pass to the server
            $this->integrityHash = (new Crypt($this->client))->encryption($options);

            $this->data = array(
                'data' => $this->integrityHash,
                'encrypted' => 1,
                'target_api' => $this->client->getTarget_api(),
                'application_name' => $this->client->getApplication_name(),
                'authorization' => $bearerTkn,
            );

        } else {

            $this->data = array(
                'data' => $options,
                'encrypted' => 0,
                'target_api' => $this->client->getTarget_api(),
                'application_name' => $this->client->getApplication_name(),
                'authorization' => $bearerTkn,
            );

        }

        // the result returned requires validation
        return $this->do($requestType);

    }


    /**
     * gets the generic message, could be an error too
     * @return string
     */
    public function getMessage()
    {

        if($this->newData->message !== null){
            return $this->newData->message;
        }


        return $this->newData->data ?? 'no message';

    }


    public function getProject(): ?string
    {
        if ($this->hasExected && $this->newData->status === true) {
            return $this->newData->project;
        }
        $this->execute($this->oldData);
        return $this->getProject();
    }


    /**
     * @return mixed
     * @throws \JsonException
     */
    public function getStatus(): ?bool
    {
        if ($this->hasExected) {

            return $this->newData->status ?? false;

        }
        $this->execute($this->oldData);
        return $this->getStatus();
    }

    /**
     * @return mixed
     */
    public function getRootUrl()
    {
        return $this->rootUrl;
    }


}