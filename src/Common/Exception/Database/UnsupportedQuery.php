<?php

declare(strict_types=1);

namespace Litebase\Common\Exception\Database;

use Litebase\Common\Exception\LitebaseException;
use Litebase\Service\Datastore\Query;
use RuntimeException;
use Throwable;

final class UnsupportedQuery extends RuntimeException implements LitebaseException
{
    private Query $query;

    public function __construct(Query $query, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->query = $query;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }
}
