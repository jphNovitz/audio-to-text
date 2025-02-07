<?php
namespace App\Service;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;


class AudioConverterService
{
    private FFMpeg $ffmpeg;

    public function __construct()
    {
        // Configure les chemins vers ffmpeg et ffprobe selon ton environnement
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 3600, // optionnel
            'ffmpeg.threads'   => 12,   // optionnel
        ]);
    }

    /**
     * Convertit un fichier audio quel que soit son format (tant qu'il est supporté)
     * en fichier OGG.
     *
     * @param string $inputPath  Chemin vers le fichier source
     * @param string $outputPath Chemin où enregistrer le fichier OGG converti
     */
    public function convertToMp3(string $inputPath, string $outputPath): void
    {
        $audio = $this->ffmpeg->open($inputPath);
        $audio->save(new Mp3(), $outputPath);
    }
}
