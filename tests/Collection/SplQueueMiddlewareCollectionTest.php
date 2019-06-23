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

namespace NoGlitchYo\MiddlewareCollectionRequestHandler\Tests\Collection;

use NoGlitchYo\MiddlewareCollectionRequestHandler\Exception\EmptyMiddlewareCollectionException;
use NoGlitchYo\MiddlewareCollectionRequestHandler\Tests\GetMiddlewareTrait;
use NoGlitchYo\MiddlewareCollectionRequestHandler\Collection\SplQueueMiddlewareCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \NoGlitchYo\MiddlewareCollectionRequestHandler\Collection\SplQueueMiddlewareCollection
 */
class SplQueueMiddlewareCollectionTest extends TestCase
{
    use GetMiddlewareTrait;

    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    protected function setUp(): void
    {
        $this->middleware = self::getMiddleware();
    }

    public function testConstructCallAdd()
    {
        $middlewareCollection = new SplQueueMiddlewareCollection(
            [
                self::getMiddleware()
            ]
        );

        $this->assertFalse($middlewareCollection->isEmpty());
    }

    public function testEmpty()
    {
        $middlewareCollection = new SplQueueMiddlewareCollection();
        $this->assertTrue($middlewareCollection->isEmpty());

        return $middlewareCollection;
    }

    /**
     * @depends testEmpty
     */
    public function testNextThrowExceptionIfEmptyCollection(SplQueueMiddlewareCollection $middlewareCollection)
    {
        $this->expectException(EmptyMiddlewareCollectionException::class);
        $middlewareCollection->next();
    }

    /**
     * @depends testEmpty
     */
    public function testAdd(SplQueueMiddlewareCollection $middlewareCollection)
    {
        $middlewareCollection->add($this->middleware);
        $this->assertFalse($middlewareCollection->isEmpty());

        return $middlewareCollection;
    }

    /**
     * @depends testAdd
     */
    public function testNextRemoveMiddlewareFromCollectionAndReturnMiddleware(SplQueueMiddlewareCollection $middlewareCollection)
    {
        $this->assertEquals($this->middleware, $middlewareCollection->next());
        $this->assertTrue($middlewareCollection->isEmpty());
    }

    public function testNextGetFirstInsertedMiddleware()
    {
        $middlewareCollection = new SplQueueMiddlewareCollection();

        $middleware1 = self::getMiddleware();
        $middleware2 = self::getMiddleware();
        
        $middlewareCollection->add($middleware1);
        $middlewareCollection->add($middleware2);

        $this->assertSame($middleware1, $middlewareCollection->next());
    }
}
