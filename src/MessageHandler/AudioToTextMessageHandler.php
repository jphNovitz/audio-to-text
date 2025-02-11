<?php

namespace App\MessageHandler;

use App\Message\AudioToTextMessage;
use App\Service\WhisperService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AudioToTextMessageHandler
{
    public function __construct(private WhisperService $whisperService)
    {
    }
    public function __invoke(AudioToTextMessage $message): string
    {
        try {
            $transcription = $this->whisperService->transcribeAudio();

        } catch (\Exception $e) {
            dump('Erreur lors de la publication:', $e->getMessage());
        }
    }
}
