<?php

declare(strict_types=1);

namespace Litebase\Common\Exception\Database;


use Litebase\Common\Exception\LitebaseException;
use Litebase\Service\Datastore\Reference;
use RuntimeException;
use Throwable;

final class ReferenceHasNotBeenSnapshotted extends RuntimeException implements LitebaseException
{
    private Reference $reference;

    public function __construct(Reference $query, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (!$message) {
            $message = "The reference {$query->getPath()} has not been snapshotted.";
        }

        parent::__construct($message, $code, $previous);

        $this->reference = $query;
    }

    public function getReference(): Reference
    {
        return $this->reference;
    }
}
