<?php

namespace App\Message;

final class AudioSplitMessage
{
    public function __construct(
        public string $inputFile = "",
        public string $outputPath = "",
    ) {
    }


}
