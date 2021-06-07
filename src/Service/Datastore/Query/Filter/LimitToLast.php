<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query\Filter;


use Litebase\Common\Exception\InvalidArgumentException;
use Litebase\Service\Datastore\Query\Filter;
use Litebase\Service\Datastore\Query\ModifierTrait;
use Psr\Http\Message\UriInterface;

final class LimitToLast implements Filter
{
    use ModifierTrait;

    private int $limit;

    public function __construct(int $limit)
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Limit must be 1 or greater');
        }

        $this->limit = $limit;
    }

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'limitToLast', $this->limit);
    }
}
