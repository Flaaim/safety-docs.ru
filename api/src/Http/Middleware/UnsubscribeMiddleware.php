<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use App\Http\JsonResponse;

final class UnsubscribeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly LoggerInterface $logger,
    ) {
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $rawBody = $request->getBody()->getContents();
        $data = $request->getParsedBody() ?? [];
        $apiKey = $this->container->get('config')['uniSender']['apiKey'];

        if (!isset($data['auth'])) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $receivedHash = $data['auth'];
        $stringForCheck = str_replace($receivedHash, $apiKey, $rawBody);

        $calculatedHash = md5($stringForCheck);
        if (!hash_equals($calculatedHash, $receivedHash)) {
            $this->logger->error('Auth in unsubscribed request invalid.', [
                'received' => $receivedHash,
                'calculated' => $calculatedHash,
            ]);
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $emails = [];
        if (isset($data['events_by_user'])) {
            foreach ($data['events_by_user'] as $user) {
                foreach ($user['events'] as $event) {
                    if ($event['event_name'] === 'transactional_email_status') {
                        $status = $event['event_data']['status'] ?? '';

                        if (in_array($status, ['unsubscribed', 'spam_rejected'], true)) {
                            if (!empty($event['event_data']['email'])) {
                                $emails[] = $event['event_data']['email'];
                            }
                        }
                    } elseif ($event['event_name'] === 'transactional_spam_block') {
                        $this->logger->critical('ВНИМАНИЕ! UniSender заблокировал отправку (Spam Block)!', [
                            'event_data' => $event['event_data'],
                        ]);
                    }
                }
            }
        }
        $request = $request->withParsedBody($emails);

        return $handler->handle($request);
    }
}
