<?php

declare(strict_types=1);

namespace Litebase\Common\Exception\Database;


use Litebase\Common\Exception\LitebaseException;
use Litebase\Service\Datastore\Reference;
use RuntimeException;
use Throwable;

final class TransactionFailed extends RuntimeException implements LitebaseException
{
    private Reference $reference;

    public function __construct(Reference $query, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (\trim($message) === '') {
            $queryPath = $query->getPath();

            $message = "The transaction on {$queryPath} failed";

            if ($previous instanceof PreconditionFailed) {
                $message = "The reference {$queryPath} has changed remotely since the transaction has been started.";
            } elseif ($previous !== null) {
                $message = "The transaction on {$query->getPath()} failed: {$previous->getMessage()}";
            }
        }

        parent::__construct($message, $code, $previous);

        $this->reference = $query;
    }

    public static function onReference(Reference $reference, ?Throwable $error = null): self
    {
        $code = $error ? $error->getCode() : 0;

        return new self($reference, '', $code, $error);
    }

    public function getReference(): Reference
    {
        return $this->reference;
    }
}
