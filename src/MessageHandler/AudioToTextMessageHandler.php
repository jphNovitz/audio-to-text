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
    public function __invoke(AudioToTextMessage $message): bool
    {
        try {
            $transcription = $this->whisperService->transcribeAudio();
            return true;

        } catch (\Exception $e) {
            return false;
//            dump('Erreur lors de la publication:', $e->getMessage());
        }
    }
}
