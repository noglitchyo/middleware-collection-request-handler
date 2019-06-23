<?php declare(strict_types=1);
/**
 * MIT License
 *
 * Copyright (c) 2019 Maxime Elomari
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace NoGlitchYo\MiddlewareCollectionRequestHandler\Collection;

use NoGlitchYo\MiddlewareCollectionRequestHandler\Exception\EmptyMiddlewareCollectionException;
use NoGlitchYo\MiddlewareCollectionRequestHandler\MiddlewareCollectionInterface;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;
use SplStack;

class SplStackMiddlewareCollection implements MiddlewareCollectionInterface
{
    /**
     * @var SplStack
     */
    private $stack;

    /**
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(array $middlewares = [])
    {
        $this->stack = new SplStack();

        foreach ($middlewares as $middleware) {
            $this->add($middleware);
        }
    }

    public function add(MiddlewareInterface $middleware): MiddlewareCollectionInterface
    {
        $this->stack->push($middleware);

        return $this;
    }

    public function isEmpty(): bool
    {
        return $this->stack->isEmpty();
    }

    /**
     * @throws EmptyMiddlewareCollectionException
     */
    public function next(): MiddlewareInterface
    {
        try {
            return $this->stack->pop();
        } catch (RuntimeException $e) {
            throw new EmptyMiddlewareCollectionException();
        }
    }
}
