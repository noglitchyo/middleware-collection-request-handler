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

namespace NoGlitchYo\MiddlewareCollectionRequestHandler\Tests;

use NoGlitchYo\MiddlewareCollectionRequestHandler\MiddlewareCollectionInterface;
use NoGlitchYo\MiddlewareCollectionRequestHandler\RequestHandler;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \NoGlitchYo\MiddlewareCollectionRequestHandler\RequestHandler
 */
class RequestHandlerTest extends TestCase
{
    use GetMiddlewareTrait;

    /**
     * @var MiddlewareCollectionInterface|MockObject
     */
    private $middlewareCollectionMock;

    /**
     * @var MockObject|RequestHandlerInterface
     */
    private $requestHandlerMock;

    /**
     * @var RequestHandler
     */
    private $sut;

    protected function setUp(): void
    {
        $this->requestHandlerMock = $this->createMock(RequestHandlerInterface::class);
        $this->middlewareCollectionMock = $this->createMock(MiddlewareCollectionInterface::class);

        $this->sut = new RequestHandler($this->requestHandlerMock, $this->middlewareCollectionMock);
    }

    public function testHandleDelegateToDefaultHandlerIfNoMiddleware()
    {
        $request = new ServerRequest('GET', '/test');
        $response = new Response(404);

        $this->middlewareCollectionMock->method('isEmpty')
            ->willReturn(true);

        $this->requestHandlerMock->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturnCallback(
                function (ServerRequestInterface $request) use ($response) {
                    return $response;
                }
            );

        $this->assertSame($response, $this->sut->handle($request));
    }

    public function testIsCallable()
    {
        $request = new ServerRequest('GET', '/test');
        $response = new Response(404);

        $this->middlewareCollectionMock->method('isEmpty')
            ->willReturn(true);

        $this->requestHandlerMock->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturnCallback(
                function (ServerRequestInterface $request) use ($response) {
                    return $response;
                }
            );

        $this->assertSame($response, call_user_func($this->sut, $request));
    }

    public function testHandleCallNextMiddlewareAndReturnResponse()
    {
        $request = new ServerRequest('GET', '/test');

        $this->middlewareCollectionMock
            ->expects($this->once())
            ->method('isEmpty')
            ->willReturn(false);

        $this->middlewareCollectionMock
            ->expects($this->once())
            ->method('next')
            ->willReturn(self::getMiddleware(true));

        $this->assertInstanceOf(ResponseInterface::class, $this->sut->handle($request));
    }

    public function testHandleCallNextMiddlewareWithInstanceOfThisAsHandler()
    {
        $request = new ServerRequest('GET', '/test');

        $this->middlewareCollectionMock
            ->expects($this->exactly(2))
            ->method('isEmpty')
            ->willReturn(false);

        $this->middlewareCollectionMock
            ->expects($this->exactly(2))
            ->method('next')
            ->willReturnOnConsecutiveCalls(
                self::getMiddleware(false),
                self::getMiddleware(true),
                );

        $this->assertInstanceOf(ResponseInterface::class, $this->sut->handle($request));
    }

    public function testFromCallableCreateDefaultRequestHandlerFromCallable()
    {
        $request = new ServerRequest('GET', '/test');
        $response = new Response(405);
        $callable = function () use ($response) {
            return $response;
        };

        $this->middlewareCollectionMock->method('isEmpty')
            ->willReturn(true);

        $sut = $this->sut::fromCallable($callable, $this->middlewareCollectionMock);

        $this->assertSame($response, $sut->handle($request));
    }


}
