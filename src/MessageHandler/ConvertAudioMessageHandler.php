<?php

namespace App\MessageHandler;

use App\Message\ConvertAudioMessage;
use App\Contract\AudioConverterServiceInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConvertAudioMessageHandler
{
    public function __construct(private AudioConverterServiceInterface $audioConverterService, private HubInterface $hub)
    {
    }

    public function __invoke(ConvertAudioMessage $message): void
    {
//        $this->audioConverterService->convertToMp3();
    }
}
