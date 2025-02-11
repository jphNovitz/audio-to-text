<?php

namespace App\Service;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Audio\Mp3;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class AudioSplitterService
{
    private $ffmpeg;
    private $filesystem;
    private const SEGMENT_DURATION = 300;
    private string $baseDir;
    public function __construct(string $baseDir, private HubInterface $hub)
    {
        $this->baseDir = $baseDir;

        $this->filesystem = new Filesystem();
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout' => 3600,
            'ffmpeg.threads' => 12,
        ]);
    }

    /**
     * Découpe un fichier audio en segments de 10 minutes
     *
     * @param string $inputFile Chemin du fichier à découper
     * @param string $outputDir Dossier où stocker les segments
     * @return array Liste des chemins des segments créés
     */
    public function split(string $inputFile, string $outputDir = "audio_segments"): array
    {
        $tempPath = Path::normalize($this->baseDir . '/var/tmp/' . $outputDir);

        $this->filesystem->remove($tempPath);
        if (!file_exists($inputFile)) {
            throw new \RuntimeException("Le fichier source n'existe pas");
        }

        // Créer le dossier de sortie s'il n'existe pas
        $this->filesystem->mkdir($tempPath);

//        $this->filesystem->mkdir($outputDir);

        try {
            $duration = $this->getDuration($inputFile);
            $numberOfSegments = ceil($duration / self::SEGMENT_DURATION);
            $outputFiles = [];

            for ($i = 0; $i < $numberOfSegments; $i++) {
                $startTime = $i * self::SEGMENT_DURATION;
                $segmentDuration = min(self::SEGMENT_DURATION, $duration - $startTime);

                $outputFile = sprintf(
                    '%s/segment_%03d.mp3',
                    rtrim($tempPath, '/'),
                    $i + 1
                );

                $this->createSegment($inputFile, $outputFile, $startTime, $segmentDuration);
                $outputFiles[] = $outputFile;

                try {
                    $update = new Update(
                        'progression',
                        json_encode(['message' => 'true'])
                    );

                    $this->hub->publish($update);

                } catch (\Exception $e) {
                    dump('Erreur lors de la publication:', $e->getMessage());
                }
            }

            return $outputFiles;

        } catch (\Exception $e) {
//            dd($e);
            throw new \RuntimeException("Erreur lors du découpage audio: " . $e->getMessage());
        }
    }

    private function getDuration(string $filename): float
    {
        $ffprobe = $this->ffmpeg->getFFProbe();
        return (float) $ffprobe->format($filename)->get('duration');
    }

    private function createSegment(
        string $inputFile,
        string $outputFile,
        float $startTime,
        float $duration
    ): void {
        $audio = $this->ffmpeg->open($inputFile);
        $audio->filters()->clip(
            TimeCode::fromSeconds($startTime),
            TimeCode::fromSeconds($duration)
        );

        $format = new Mp3();
        $format->setAudioChannels(2)
            ->setAudioKiloBitrate(256);

        $audio->save($format, $outputFile);
    }
}