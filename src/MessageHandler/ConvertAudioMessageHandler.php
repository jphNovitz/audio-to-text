<?php

namespace App\MessageHandler;

use App\Message\ConvertAudioMessage;
use App\Service\AudioConverterService;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConvertAudioMessageHandler
{
    public function __construct(private AudioConverterService $audioConverterService, private HubInterface $hub)
    {
    }

    public function __invoke(ConvertAudioMessage $message): void
    {
        if ($this->audioConverterService->convertToMp3()) {

            try {
                $update = new Update(
                    'progression',
                    json_encode(['message' => 'true'])
                );

                $this->hub->publish($update);

            } catch (\Exception $e) {
                dump('Erreur lors de la publication:', $e->getMessage());
            }
        }
    }
}
