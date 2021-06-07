<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore;


use Litebase\Common\Exception\InvalidArgumentException;
use Litebase\Common\Http\Uri;
use Litebase\LitebaseClient;
use Litebase\LitebaseService;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class Database extends LitebaseService
{
    public const SERVER_TIMESTAMP = ['.sv' => 'timestamp'];

    protected static string $databaseUriPattern = 'http://broos.cloud/api/db/%s';

    private ApiClient $apiClient;

    private UriInterface $uri;

    protected ?UriInterface $databaseUri = null;

    /**
     * @param UriInterface $uri
     * @param ApiClient $client
     * @internal
     */
    public function __construct(UriInterface $uri, ApiClient $client)
    {
        parent::__construct($client->getClient());
        $this->uri = $uri;
        $this->rootUrl = $uri;
        $client->setLitebaseService($this);
        $this->apiClient = $client;
    }

    public function getReference(?string $path = null): Reference
    {
        if ($path === null || \trim($path) === '') {
            $path = '/';
        }

        try {
            return new Reference($this->uri->withPath($path), $this->apiClient);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getReferenceFromUrl($uri): Reference
    {
        $uri = $uri instanceof UriInterface ? $uri : new Uri($uri);

        if (($givenHost = $uri->getHost()) !== ($dbHost = $this->uri->getHost())) {
            throw new InvalidArgumentException(\sprintf(
                'The given URI\'s host "%s" is not covered by the database for the host "%s".',
                $givenHost,
                $dbHost
            ));
        }

        return $this->getReference($uri->getPath());
    }

    public function getRuleSet(): RuleSet
    {
        $rules = $this->apiClient->get($this->uri->withPath('settings/rules'));

        return RuleSet::fromArray($rules);
    }

    public function updateRules(RuleSet $ruleSet): void
    {
        $this->apiClient->updateRules($this->uri->withPath('settings/rules'), $ruleSet);
    }

    public function runTransaction(callable $callable)
    {
        $transaction = new Transaction($this->apiClient);

        return $callable($transaction);
    }

    public function withDatabaseUri($uri): self
    {
        $factory = clone $this;
        $factory->databaseUri = Uri::uriFor($uri);

        return $factory;
    }

    public static function getDatabaseUri(LitebaseClient $client): UriInterface
    {

        if ($client->getClient_secret() !== '') {
            return Uri::uriFor(\sprintf(self::$databaseUriPattern, $client->getClient_id()));
        }

        throw new RuntimeException('Unable to build a database URI without a project ID');
    }

    public static function createDatabase(LitebaseClient $client)
    {
        return new Database(self::getDatabaseUri($client), new ApiClient($client));
    }
}
