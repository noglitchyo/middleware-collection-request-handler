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

use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface, MiddlewareInterface
{
    use RequestHandlerTrait;

    /**
     * @var MiddlewareCollectionInterface
     */
    private $middlewareCollection;

    /**
     * @var RequestHandlerInterface|null
     */
    private $defaultRequestHandler;

    public function __construct(
        MiddlewareCollectionInterface $middlewareCollection,
        RequestHandlerInterface $defaultRequestHandler = null
    ) {
        $this->middlewareCollection  = $middlewareCollection;
        $this->defaultRequestHandler = $defaultRequestHandler;
    }

    public static function fromCallable(
        callable $callable,
        MiddlewareCollectionInterface $middlewareCollection
    ): self {
        $defaultRequestHandler = static::createRequestHandlerFromCallable($callable);
        return new static($middlewareCollection, $defaultRequestHandler);
    }

    public function __invoke(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->handle($serverRequest);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->defaultRequestHandler === null) {
            throw new LogicException(
                'A default request handler must be defined if RequestHandler is used as a RequestHandler.'
            );
        }

        if ($this->middlewareCollection->isEmpty()) {
            return $this->defaultRequestHandler->handle($request);
        }

        $nextMiddleware = $this->middlewareCollection->next();

        return $nextMiddleware->process($request, $this);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!isset($this->defaultRequestHandler)) {
            $this->defaultRequestHandler = $handler;
        }

        if ($this->middlewareCollection->isEmpty()) {
            return $handler->handle($request);
        }

        $nextMiddleware = $this->middlewareCollection->next();

        return $nextMiddleware->process($request, $this);
    }
}
