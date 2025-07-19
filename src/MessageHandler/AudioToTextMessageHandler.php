<?php

namespace App\MessageHandler;

use App\Message\AudioToTextMessage;
use App\Contract\MessagePublisherInterface;
use App\Contract\WhisperServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AudioToTextMessageHandler
{
    public function __construct(private WhisperServiceInterface $whisperService, private MessagePublisherInterface $messagePublisher)
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
