<?php
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
declare(strict_types=1);

namespace NoGlitchYo\MiddlewareCollectionRequestHandler;

use NoGlitchYo\MiddlewareCollectionRequestHandler\Exception\EmptyMiddlewareCollectionException;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Represent a collection of MiddlewareInterface instances.
 * The collection can implement the method for processing data of its choice: LIFO, FIFO...
 *
 * @codeCoverageIgnore
 */
interface MiddlewareCollectionInterface
{
    /**
     * Must return true if there is no middleware in the collection to process.
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * MUST return the next middleware to process in the collection.
     * Depending on the implemented strategy, the middleware MAY not be removed from the collection.
     *
     * MUST throws EmptyMiddlewareCollectionException if there is not next middleware to return.
     *
     * @throws EmptyMiddlewareCollectionException
     *
     * @return MiddlewareInterface
     */
    public function next(): MiddlewareInterface;

    /**
     * Add a middleware instance of MiddlewareInterface to the collection.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return MiddlewareCollectionInterface
     */
    public function add(MiddlewareInterface $middleware): MiddlewareCollectionInterface;
}
