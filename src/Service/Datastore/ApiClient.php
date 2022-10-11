<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore;



use Litebase\Common\Crypto\Crypt;
use Litebase\Common\Http\Request;
use Litebase\Common\Http\Request\Body;
use Litebase\Common\Util\JSON;
use Litebase\LitebaseClient;
use Litebase\LitebaseService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

/**
 * @internal
 */
class ApiClient
{
    protected $client;
    protected LitebaseService $litebaseService;

    /**
     * @param LitebaseClient $litebaseClient
     * @internal
     */
    public function __construct(LitebaseClient $litebaseClient)
    {
        $this->client = $litebaseClient;
    }

    /**
     * @param UriInterface|string $uri
     *
     *
     * @return mixed
     */
    public function get($uri)
    {
        $response = $this->requestApi('GET', $uri);

        return JSON::decode((string)$response->getBody(), true);
    }

    /**
     * @param UriInterface|string $uri
     *
     * @return array<string, mixed>
     * @internal This method should only be used in the context of Database translations
     *
     */
    public function getWithETag($uri): array
    {
        $response = $this->requestApi('GET', $uri, [
            'headers' => [
                'X-Litebase-ETag' => 'true',
            ],
        ]);

        $value = JSON::decode((string)$response->getBody(), true);
        $etag = $response->getHeaderLine('ETag');

        return [
            'value' => $value,
            'etag' => $etag,
        ];
    }

    /**
     * @param UriInterface|string $uri
     * @param mixed $value
     * @return mixed
     */
    public function set($uri, $value)
    {
        $response = $this->requestApi('PUT', $uri, ['json' => $value]);

        return JSON::decode((string)$response->getBody(), true);
    }

    /**
     * @param UriInterface|string $uri
     * @param mixed $value
     *
     * @return mixed
     * @internal This method should only be used in the context of Database translations
     *
     */
    public function setWithEtag($uri, $value, string $etag)
    {
        $response = $this->requestApi('PUT', $uri, [
            'headers' => [
                'if-match' => $etag,
            ],
            'json' => $value,
        ]);

        return JSON::decode((string)$response->getBody(), true);
    }

    /**
     * @param UriInterface|string $uri
     *
     * @internal This method should only be used in the context of Database translations
     *
     */
    public function removeWithEtag($uri, string $etag): void
    {
        $this->requestApi('DELETE', $uri, [
            'headers' => [
                'if-match' => $etag,
            ],
        ]);
    }

    /**
     * @param UriInterface|string $uri
     * @param RuleSet $ruleSet
     * @return mixed
     */
    public function updateRules($uri, RuleSet $ruleSet)
    {
        $response = $this->requestApi('PUT', $uri, [
            'json' => \json_encode($ruleSet, \JSON_PRETTY_PRINT),
        ]);

        return JSON::decode((string)$response->getBody(), true);
    }

    /**
     * @param UriInterface|string $uri
     * @param mixed $value
     *
     */
    public function push($uri, $value): string
    {
        $response = $this->requestApi('POST', $uri, ['json' => $value]);

        return JSON::decode((string)$response->getBody(), true)['name'];
    }

    /**
     * @param UriInterface|string $uri
     *
     */
    public function remove($uri): void
    {
        $this->requestApi('DELETE', $uri);
    }

    /**
     * @param UriInterface|string $uri
     * @param array<mixed> $values
     *
     */
    public function update($uri, array $values): void
    {
        $this->requestApi('PATCH', $uri, ['json' => $values]);
    }

    /**
     * @return LitebaseService
     */
    public function getLitebaseService(): LitebaseService
    {
        return $this->litebaseService;
    }

    /**
     * @param LitebaseService $litebaseService
     */
    public function setLitebaseService(LitebaseService $litebaseService): void
    {
        $this->litebaseService = $litebaseService;
    }



    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @param array<string, mixed>|null $options
     * @return ResponseInterface
     */
    private function requestApi(string $method, $uri, ?array $options = null): ResponseInterface
    {
        $options = $options ?? [];
        $body = [];
        //contains additional operations
        $body['json'] = $options['json'] ?? [];
        //only for reference, the actual rule-sets are known in litebase cloud
        $body['rules'] = RuleSet::default()->jsonSerialize()['rules'];

        //this mini body is encrypted as it is the payload
        $integrityHash = (new Crypt($this->client))->encryption($body);

        //this payload is always needed for database operations
        $data = array(
            'data' => $integrityHash,
            'encrypted' => 1,
            'target_api' => $this->client->getTarget_api(),
            'application_name' => $this->client->getApplication_name(),
            'authorization' => $this->client->getClient_id(),
        );

        $headers = $options['headers'] ?? [];
        $headers['Content-Type'] = 'application/json';
        $headers['authorization'] = $this->client->getClient_id();
        $headers['Authorization'] = $this->client->getClient_id();
        $headers['user-agent'] = $this->client::USER_AGENT;
        $headers['username'] = $this->client->getConfig()['username'];

        $reqbody = Body::Json($data);

        $uri = LitebaseClient::API_BASE_PATH.Database::getDatabaseUri($this->client).$uri;

        $response = null;
        if ($method === 'POST') {
            $response = Request::post($uri, $headers, $reqbody);
        }

        if ($method === 'PUT') {
            $response = Request::put($uri, $headers, $reqbody);
        }

        if ($method === 'DELETE') {
            $response = Request::delete($uri, $headers, $reqbody);
        }

        if ($method === 'GET') {
            $response = Request::get($uri, $headers, $reqbody);
        }

        if ($method === 'PATCH') {
            $response = Request::patch($uri, $headers, $reqbody);
        }

       

        return $response;
    }

    /**
     * @return LitebaseClient
     */
    public function getClient(): LitebaseClient
    {
        return $this->client;
    }


}
