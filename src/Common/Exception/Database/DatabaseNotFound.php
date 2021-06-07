<?php

declare(strict_types=1);

namespace Litebase\Common\Exception\Database;


use Litebase\Common\Exception\LitebaseException;
use LogicException;
use Psr\Http\Message\UriInterface;

final class DatabaseNotFound extends LogicException implements LitebaseException
{
    public static function fromUri(UriInterface $uri): self
    {
        $scheme = $uri->getScheme();
        $host = $uri->getHost();

        $databaseName = \explode('.', $host, 2)[0] ?? '';

        $databaseUri = "{$scheme}://{$host}";
        $suggestedDatabaseUri = \str_replace($databaseName, $databaseName.'-default-rtdb', $databaseUri);

        $message = <<<MESSAGE


            The database at

                {$databaseUri}

            could not be found. You can find the correct name at

            The reason for this is that you are either calling the wrong database endpoint
            or your project's public key just has to be changed.

            MESSAGE;

        return new self($message);
    }
}
