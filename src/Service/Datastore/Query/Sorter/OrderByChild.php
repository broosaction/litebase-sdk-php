<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query\Sorter;

use Litebase\Service\Datastore\Query\ModifierTrait;
use Litebase\Service\Datastore\Query\Sorter;
use function JmesPath\search;

use Psr\Http\Message\UriInterface;

final class OrderByChild implements Sorter
{
    use ModifierTrait;

    private string $childKey;

    public function __construct(string $childKey)
    {
        $this->childKey = $childKey;
    }

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'orderBy', \sprintf('"%s"', $this->childKey));
    }

    public function modifyValue($value)
    {
        if (!\is_array($value)) {
            return $value;
        }

        $expression = \str_replace('/', '.', $this->childKey);

        \uasort($value, static fn ($a, $b) => search($expression, $a) <=> search($expression, $b));

        return $value;
    }
}
