<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query;


use Litebase\Common\Http\Query;
use Psr\Http\Message\UriInterface;

/**
 * @codeCoverageIgnore
 */
trait ModifierTrait
{
    /**
     * @param UriInterface $uri
     * @param string $key
     * @param mixed $value
     * @return UriInterface
     */
    protected function appendQueryParam(UriInterface $uri, string $key, $value): UriInterface
    {
        $queryParams = \array_merge(Query::parse($uri->getQuery()), [$key => $value]);

        $queryString = Query::build($queryParams);

        return $uri->withQuery($queryString);
    }

    public function modifyValue($value)
    {
        return $value;
    }
}
