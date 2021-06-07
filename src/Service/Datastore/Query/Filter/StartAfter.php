<?php

declare(strict_types=1);

namespace Litebase\Service\Datastore\Query\Filter;


use Litebase\Common\Exception\InvalidArgumentException;
use Litebase\Common\Util\JSON;
use Litebase\Service\Datastore\Query\Filter;
use Litebase\Service\Datastore\Query\ModifierTrait;
use Psr\Http\Message\UriInterface;

final class StartAfter implements Filter
{
    use ModifierTrait;

    /** @var int|float|string|bool */
    private $value;

    /**
     * @param int|float|string|bool $value
     */
    public function __construct($value)
    {
        if (!\is_scalar($value)) {
            throw new InvalidArgumentException('Only scalar values are allowed for "startAfter" queries.');
        }

        $this->value = $value;
    }

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'startAfter', JSON::encode($this->value));
    }
}
