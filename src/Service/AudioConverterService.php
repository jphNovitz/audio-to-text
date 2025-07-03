<?php

namespace App\Service;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Service\MessagePublisher;


class AudioConverterService
{
    private FFMpeg $ffmpeg;


    public function __construct(private readonly string $inputPath,
                                private readonly string $outputPath,
                                private MessagePublisher $messagePublisher)
    {
        // Configure les chemins vers ffmpeg et ffprobe selon ton environnement
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout' => 3600, // optionnel
            'ffmpeg.threads' => 12,   // optionnel
        ]);
    }


    public function convertToMp3(string $filename): string | bool
    {
        $inputFile = $this->inputPath . '/' . $filename;

        if (!file_exists($inputFile) ||
            !is_readable($inputFile) ||
            !is_dir($outputDir = dirname($this->outputPath)) ||
            !is_writable($outputDir)) {
            return false;
        }

        try {
            $audio = $this->ffmpeg->open($inputFile);
            $format = new Mp3();
            $format->setAudioKiloBitrate(128);

           $audio->save($format, $this->outputPath);

            if (file_exists($this->outputPath)){
              return $this->outputPath;
            } else 
                return false;
            

        } catch (\Exception $e) {
//            dd($e->getMessage());
//            $this->logger?->error('Erreur conversion MP3', ['error' => $e->getMessage()]);
            return false;
        }
    }
//    private function publishProgress(bool $progression = false, string $message = '', string $status = ''): void
//    {
//        $this->messagePublisher->publishProgress($progression, $message, $status);
//    }

}
