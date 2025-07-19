<?php

namespace App\MessageHandler;

use App\Message\AudioSplitMessage;
use App\Contract\AudioSplitterServiceInterface;
use App\Contract\MessagePublisherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AudioSplitMessageHandler
{
    public function __construct(private AudioSplitterServiceInterface $audioSplitterService,
                                private readonly MessagePublisherInterface $messagePublisher)
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
