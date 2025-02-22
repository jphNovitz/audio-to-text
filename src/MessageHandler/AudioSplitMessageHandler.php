<?php

namespace App\MessageHandler;

use App\Message\AudioSplitMessage;
use App\Service\AudioSplitterService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AudioSplitMessageHandler
{
    public function __construct(private AudioSplitterService $audioSplitterService)
    {
    }
    public function __invoke(AudioSplitMessage $message): void
    {
        $this->audioSplitterService->split();

    }
}
