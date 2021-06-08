<?php

declare(strict_types=1);


namespace Litebase\Service\Datastore;



use Litebase\Common\Exception\Database\ReferenceHasNotBeenSnapshotted;
use Litebase\Common\Exception\Database\TransactionFailed;
use Litebase\Common\Exception\LitebaseException;

class Transaction
{
    private ApiClient $apiClient;

    /** @var string[] */
    private array $etags;

    /**
     * @param ApiClient $apiClient
     * @internal
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->etags = [];
    }

    /**
     * @param Reference $reference
     * @return Snapshot
     */
    public function snapshot(Reference $reference): Snapshot
    {
        $uri = (string) $reference->getUri();

        $result = $this->apiClient->getWithETag($uri);

        $this->etags[$uri] = $result['etag'];

        return new Snapshot($reference, $result['value']);
    }

    /**
     * @param Reference $reference
     * @param mixed $value
     *
     */
    public function set(Reference $reference, $value): void
    {
        $etag = $this->getEtagForReference($reference);

        try {
            $this->apiClient->setWithEtag($reference->getUri(), $value, $etag);
        } catch (LitebaseException $e) {
            throw TransactionFailed::onReference($reference, $e);
        }
    }

    /**
     * @param Reference $reference
     */
    public function remove(Reference $reference): void
    {
        $etag = $this->getEtagForReference($reference);

        try {
            $this->apiClient->removeWithEtag($reference->getUri(), $etag);
        } catch (LitebaseException $e) {
            throw TransactionFailed::onReference($reference, $e);
        }
    }

    /**
     * @param Reference $reference
     * @return string
     */
    private function getEtagForReference(Reference $reference): string
    {
        $uri = (string) $reference->getUri();

        if (\array_key_exists($uri, $this->etags)) {
            return $this->etags[$uri];
        }

        throw new ReferenceHasNotBeenSnapshotted($reference);
    }
}
