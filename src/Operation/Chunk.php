<?php

declare(strict_types=1);

namespace loophp\collection\Operation;

use ArrayIterator;
use Closure;
use Generator;
use Iterator;
use loophp\collection\Contract\Operation;
use loophp\collection\Transformation\Run;

use function count;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 */
final class Chunk extends AbstractOperation implements Operation
{
    /**
     * @psalm-return Closure(int...): Closure(Iterator<TKey, T>): Generator<int, list<T>>
     */
    public function __invoke(): Closure
    {
        return
            /**
             * @psalm-param int ...$sizes
             */
            static function (int ...$sizes): Closure {
                return
                    /**
                     * @psalm-param Iterator<TKey, T> $iterator
                     *
                     * @psalm-return Generator<int, list<T>>
                     */
                    static function (Iterator $iterator) use ($sizes): Generator {
                        $sizesIterator = (new Run())()((new Loop())())(new ArrayIterator($sizes));

                        $values = [];

                        foreach ($iterator as $value) {
                            if (0 >= $sizesIterator->current()) {
                                return yield from [];
                            }

                            if (count($values) !== $sizesIterator->current()) {
                                $values[] = $value;

                                continue;
                            }

                            $sizesIterator->next();

                            yield $values;

                            $values = [$value];
                        }

                        return yield $values;
                    };
            };
    }
}
