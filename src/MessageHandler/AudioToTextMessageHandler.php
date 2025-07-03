<?php

namespace App\MessageHandler;

use App\Message\AudioToTextMessage;
use App\Service\MessagePublisher;
use App\Service\WhisperService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AudioToTextMessageHandler
{
    public function __construct(private WhisperService $whisperService, private MessagePublisher $messagePublisher)
    {
    }
    public function __invoke(AudioToTextMessage $message): bool
    {
        $this->messagePublisher->publishProgress(
            progression:true,
            status: 'Conversion audio en texte en cours...');

        try {
            $transcription = $this->whisperService->transcribeAudio();

            $this->messagePublisher->publishProgress(
                progression:true,
                status: 'fin');


            return true;

        } catch (\Exception $e) {
            return false;
//            dump('Erreur lors de la publication:', $e->getMessage());
        }
    }
}
