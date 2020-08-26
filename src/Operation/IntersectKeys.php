<?php

declare(strict_types=1);

namespace loophp\collection\Operation;

use Closure;
use Generator;
use Iterator;
use loophp\collection\Contract\Operation;

use function in_array;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 */
final class IntersectKeys extends AbstractOperation implements Operation
{
    public function __invoke(): Closure
    {
        return static function (...$values): Closure {
            return static function (Iterator $iterator) use ($values): Generator {
                foreach ($iterator as $key => $value) {
                    if (false === in_array($key, $values, true)) {
                        continue;
                    }

                    yield $key => $value;
                }
            };
        };
    }
}
