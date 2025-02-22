<?php

namespace App\Service;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


class AudioConverterService
{
    private FFMpeg $ffmpeg;

    public function __construct(private readonly string $inputPath,
                                private readonly string $outputPath,
                                private readonly HubInterface $hub)
    {
        // Configure les chemins vers ffmpeg et ffprobe selon ton environnement
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout' => 3600, // optionnel
            'ffmpeg.threads' => 12,   // optionnel
        ]);
    }


    public function convertToMp3(): bool
    {
        try {
            $audio = $this->ffmpeg->open($this->inputPath);

            $format = new Mp3();
            $format->setAudioKiloBitrate(32);
            $audio->save($format, $this->outputPath);

            try {
                $update = new Update(
                    'progression',
                    json_encode(['message' => 'true'])
                );

                $this->hub->publish($update);

            } catch (\Exception $e) {
                dump('Conversion - Erreur lors de la publication:', $e->getMessage());
            }
            return true;

        } catch (\Exception $e) {
            return false;
//            return $e->getMessage();
        }


    }
}
