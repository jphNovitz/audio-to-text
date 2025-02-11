<?php

namespace App\MessageHandler;

use App\Message\AudioSplitMessage;
use App\Service\AudioSplitterService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AudioSplitMessageHandler
{
    private string $baseDir;
    public function __construct(string $baseDir, private AudioSplitterService $audioSplitterService)
    {
        $this->baseDir = $baseDir;
    }
    public function __invoke(AudioSplitMessage $message): void
    {
        $this->audioSplitterService->split(
            $this->baseDir . '/var/tmp/audio_raw/input.m4a',
        );

    }
}
