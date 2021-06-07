<?php

declare(strict_types=1);

namespace Litebase\Common\Exception\Database;


use Litebase\Common\Exception\LitebaseException;
use RuntimeException;

final class PermissionDenied extends RuntimeException implements LitebaseException
{
}
