<?php

namespace App\MessageHandler;

use App\Message\AudioSplitMessage;
use App\Service\AudioSplitterService;
use App\Service\MessagePublisher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AudioSplitMessageHandler
{
    public function __construct(private AudioSplitterService $audioSplitterService,
                                private readonly MessagePublisher $messagePublisher)
    {
    }
    public function __invoke(AudioSplitMessage $message): void
    {

        if ($this->audioSplitterService->split($message->inputFile)) {
            $this->messagePublisher->publishProgress(
                progression:true,
                status: 'fichier découpé');

            sleep(2);

        } else
        {
            $this->messagePublisher->publishProgress(
                progression:true,
                status: 'fichier découpé');
        }

    }
}
