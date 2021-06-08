<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query\Filter;


use Litebase\Service\Datastore\Query\Filter;
use Litebase\Service\Datastore\Query\ModifierTrait;
use Psr\Http\Message\UriInterface;

final class Shallow implements Filter
{
    use ModifierTrait;

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'shallow', 'true');
    }
}
