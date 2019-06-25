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

namespace NoGlitchYo\MiddlewareCollectionRequestHandler\Tests\Collection;

use NoGlitchYo\MiddlewareCollectionRequestHandler\Exception\EmptyMiddlewareCollectionException;
use NoGlitchYo\MiddlewareCollectionRequestHandler\Tests\GetMiddlewareTrait;
use NoGlitchYo\MiddlewareCollectionRequestHandler\Collection\ArrayStackMiddlewareCollection;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \NoGlitchYo\MiddlewareCollectionRequestHandler\Collection\ArrayStackMiddlewareCollection
 */
class ArrayStackMiddlewareCollectionTest extends TestCase
{
    use GetMiddlewareTrait;

    /**
     * @var MiddlewareInterface
     */
    private static $middleware;

    public static function setUpBeforeClass(): void
    {
        self::$middleware = self::getMiddleware();
    }

    public function testConstructCallAdd()
    {
        $middlewareCollection = new ArrayStackMiddlewareCollection(
            [
                self::getMiddleware(),
            ]
        );

        $this->assertFalse($middlewareCollection->isEmpty());
    }

    public function testEmpty()
    {
        $middlewareCollection = new ArrayStackMiddlewareCollection();
        $this->assertTrue($middlewareCollection->isEmpty());

        return $middlewareCollection;
    }

    /**
     * @depends testEmpty
     */
    public function testNextThrowExceptionIfEmptyCollection(ArrayStackMiddlewareCollection $middlewareCollection)
    {
        $this->expectException(EmptyMiddlewareCollectionException::class);
        $middlewareCollection->next();
    }

    /**
     * @depends testEmpty
     */
    public function testAdd(ArrayStackMiddlewareCollection $middlewareCollection)
    {
        $middlewareCollection->add(self::$middleware);
        $this->assertFalse($middlewareCollection->isEmpty());

        return $middlewareCollection;
    }

    /**
     * @depends testAdd
     */
    public function testNextRemoveMiddlewareFromCollectionAndReturnMiddleware(
        ArrayStackMiddlewareCollection $middlewareCollection
    ) {
        $this->assertSame(self::$middleware, $middlewareCollection->next());
        $this->assertTrue($middlewareCollection->isEmpty());
    }

    public function testNextGetLastInsertedMiddleware()
    {
        $middlewareCollection = new ArrayStackMiddlewareCollection();

        $middleware1 = self::getMiddleware();
        $middleware2 = self::getMiddleware();

        $middlewareCollection->add($middleware1);
        $middlewareCollection->add($middleware2);

        $this->assertSame($middleware2, $middlewareCollection->next());
    }

    public function testAddFromCallableCreateMiddlewareAndAddItToCollection()
    {
        $middlewareCollection = new ArrayStackMiddlewareCollection();

        $middlewareCollection->addFromCallable(
            function (ServerRequestInterface $serverRequest, RequestHandlerInterface $requestHandler) {
                $this->assertTrue(true);
                return $requestHandler->handle($serverRequest);
            }
        );

        $this->assertFalse($middlewareCollection->isEmpty());

        return $middlewareCollection;
    }

    /**
     * @depends testAddFromCallableCreateMiddlewareAndAddItToCollection
     */
    public function testAddedMiddlewareCreatedFromCallableIsCallAndReturnResponse(
        ArrayStackMiddlewareCollection $middlewareCollection
    ) {
        $response = new Response();

        $requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $requestHandlerMock->expects($this->once())->method('handle')->willReturn($response);

        $this->assertSame(
            $response,
            $middlewareCollection->next()->process(
                new ServerRequest('GET', '/uri'),
                $requestHandlerMock
            )
        );
    }
}
