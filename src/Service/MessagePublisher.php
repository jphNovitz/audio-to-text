<?php

namespace App\Service;

use App\Contract\MessagePublisherInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MessagePublisher implements MessagePublisherInterface
{

    public function __construct(private readonly HubInterface $hub)
    {}

    public function publishProgress(bool $progression = false, string $message = '', string $status = ''): void
    {
        try {
            $uniqueId = uniqid();
            $update = new Update('progression', json_encode([
                'id' => $uniqueId,
                'progression' => $progression,
                'message' => $message,
                'status' => $status

            ]));

            $this->hub->publish($update);
        } catch (\Exception $e) {
//            $this->logger?->warning('Erreur publication Mercure', ['error' => $e->getMessage()]);
        }
    }
}