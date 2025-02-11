<?php

namespace App\Service;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


class AudioConverterService
{
    private FFMpeg $ffmpeg;

    public function __construct(private string $inputPath, private string $outputPath)
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
            $format->setAudioKiloBitrate(192);

            // Ajout des options threads via les filtres
            $audio->filters()->custom('threads', '4');

            $audio->save($format, $this->outputPath);
            return true;
        } catch (\Exception $e) {
            return false;
        }

       /* try {
            $audio = $this->ffmpeg->open($this->inputPath);
            $audio->save(new Mp3(), $this->outputPath);
            return true;
        } catch (\Exception $e) {
            return false;
//            dump('Erreur lors de la conversion:', $e->getMessage());
        }*/

    }
}
