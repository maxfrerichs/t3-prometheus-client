<?php

namespace MFR\T3PromClient\Middleware;

use MFR\T3PromClient\Authentication\AuthenticationFactory;
use MFR\T3PromClient\Enum\Status;
use MFR\T3PromClient\Message\PromClientRequestMessage;
use MFR\T3PromClient\Service\PrometheusService;
use Prometheus\RenderTextFormat;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
class PrometheusMiddleware implements MiddlewareInterface
{
    const EXT_KEY = 't3_prometheus_client';
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ExtensionConfiguration $config,
        private readonly PrometheusService $promService,
        private readonly AuthenticationFactory $authFactory,
        private readonly MessageBusInterface $bus,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $authentication = $this->authFactory->getAuthentication(
            $this->config->get(self::EXT_KEY)['mode']
        );

        if (!$authentication->authenticate(config: $this->config, request: $request) && $this->config->get(self::EXT_KEY)['debug'] == false) {
            $this->logger->warning("Authorization failed.");
            return $this->responseFactory->createResponse(403, 'Authorization failed.');
        }

        if (($request->getRequestTarget() != $this->config->get(self::EXT_KEY)['path'])) {
            return $handler->handle($request);
        }

        $this->bus->dispatch(
            new PromClientRequestMessage(status: Status::REQUEST_DISPATCHED)
        );

        $streamBody = $this->streamFactory->createStream(
            $this->promService->read()
        );

        return $this->responseFactory->createResponse(200)
            ->withBody($streamBody)
            ->withHeader('Content-Type', RenderTextFormat::MIME_TYPE)
            ->withHeader('Content-Length', (string)$streamBody->getSize());
    }
}
