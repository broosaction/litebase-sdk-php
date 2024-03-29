<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query\Sorter;


use Litebase\Service\Datastore\Query\ModifierTrait;
use Litebase\Service\Datastore\Query\Sorter;
use Psr\Http\Message\UriInterface;

final class OrderByKey implements Sorter
{
    use ModifierTrait;

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'orderBy', '"$key"');
    }

    public function modifyValue($value)
    {
        if (!\is_array($value)) {
            return $value;
        }

        \ksort($value);

        return $value;
    }
}
