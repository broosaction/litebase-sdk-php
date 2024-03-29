<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query\Sorter;


use Litebase\Service\Datastore\Query\ModifierTrait;
use Litebase\Service\Datastore\Query\Sorter;
use Psr\Http\Message\UriInterface;

final class OrderByValue implements Sorter
{
    use ModifierTrait;

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'orderBy', '"$value"');
    }

    public function modifyValue($value)
    {
        if (!\is_array($value)) {
            return $value;
        }

        \asort($value);

        return $value;
    }
}
