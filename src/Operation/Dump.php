<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace loophp\collection\Operation;

use Closure;
use Generator;
use Iterator;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 *
 * phpcs:disable Generic.Files.LineLength.TooLong
 */
final class Dump extends AbstractOperation
{
    /**
     * @psalm-return Closure(string): Closure(int): Closure(?Closure): Closure(Iterator<TKey, T>): Generator<TKey, T>
     */
    public function __invoke(): Closure
    {
        return
            /**
             * @psalm-return Closure(int): Closure(?Closure): Closure(Iterator<TKey, T>): Generator<TKey, T>
             */
            static fn (string $name = ''): Closure =>
                /**
                 * @psalm-return Closure(?Closure): Closure(Iterator<TKey, T>): Generator<TKey, T>
                 */
                static fn (int $size = -1): Closure =>
                    /**
                     * @psalm-return Closure(Iterator<TKey, T>): Generator<TKey, T>
                     */
                    static fn (?Closure $callback = null): Closure =>
                        /**
                         * @psalm-return Generator<TKey, T>
                         */
                        static function (Iterator $iterator) use ($name, $size, $callback): Generator {
                            $j = 0;

                            /** @var callable $debugFunction */
                            $debugFunction = class_exists(VarDumper::class) ? 'dump' : 'var_dump';

                            $callback ??=
                                /**
                                 * @param mixed $key
                                 * @param mixed $value
                                 * @psalm-param TKey $key
                                 * @psalm-param T $value
                                 *
                                 * @psalm-return mixed
                                 */
                                static fn (string $name, $key, $value) => $debugFunction(['name' => $name, 'key' => $key, 'value' => $value]);

                            foreach ($iterator as $key => $value) {
                                yield $key => $value;

                                if (-1 === $size) {
                                    continue;
                                }

                                if ($j++ < $size || 0 === $size) {
                                    $callback($name, $key, $value);

                                    continue;
                                }

                                $size = -1;
                            }
                        };
    }
}
