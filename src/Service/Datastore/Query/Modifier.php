<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query;

use Psr\Http\Message\UriInterface;

interface Modifier
{
    /**
     * Modifies the given URI and returns it.
     * @param UriInterface $uri
     * @return UriInterface
     */
    public function modifyUri(UriInterface $uri): UriInterface;

    /**
     * Modifies the given value and returns it.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function modifyValue($value);
}
