<?php

namespace App\Service;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Audio\Mp3;
use phpDocumentor\Reflection\Types\Boolean;
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
    public function __construct(private string $baseDir,
                                private string $inputPath)
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
     * Découpe un fichier audio en segments
     *
     * @param string $outputDir Dossier où stocker les segments
     */
    public function split(string $inputFile, string $outputDir = "audio_segments"): bool
    {

        $tempPath = Path::normalize($this->baseDir . '/var/tmp/' . $outputDir);
//        $inputFile = Path::normalize($this->inputPath . '/' . $rawName);
//dump($rawName);
//      dd($inputFile);

        if (!file_exists($inputFile) || !is_readable($inputFile)) {
            throw new \RuntimeException("Le fichier source n'existe pas");
        }

        if (!file_exists($tempPath)) {
            $this->filesystem->mkdir($tempPath);
        } else {
            $this->filesystem->remove(glob($tempPath . '/*'));
        }

        try {
            $duration = $this->getDuration($inputFile);
//            dump($duration / self::SEGMENT_DURATION);
            $numberOfSegments = ceil($duration / self::SEGMENT_DURATION);
//            dd($numberOfSegments);
//            $outputParts = [];

            for ($i = 0; $i < $numberOfSegments; $i++) {
                dump($i);
                $startTime = $i * self::SEGMENT_DURATION;
                $segmentDuration = min(self::SEGMENT_DURATION, $duration - $startTime);

                $outputPart = sprintf(
                    '%s/segment_%03d.mp3',
                    rtrim($tempPath, '/'),
                    $i + 1
                );

                $this->createSegment($inputFile, $outputPart, $startTime, $segmentDuration);
//                $outputParts[] = $outputPart;

//                try {
//                    $update = new Update(
//                        'progression',
//                        json_encode(['message' => 'true'])
//                    );
//
//                    $this->hub->publish($update);
//
//                } catch (\Exception $e) {
//                    dump('Erreur lors de la publication:', $e->getMessage());
//                }

            }
            return true;

        } catch (\Exception $e) {
//            dd($e->getMessage());
            return false;
//            dd($e->getMessage());
//            throw new \RuntimeException("Erreur lors du découpage audio: " . $e->getMessage());

        }
    }

    private function getDuration(string $filename): float
    {
        $ffprobe = $this->ffmpeg->getFFProbe();
        return (float) $ffprobe->format($filename)->get('duration');
    }

    private function createSegment(
        string $rawFile,
        string $outputPart,
        float $startTime,
        float $duration
    ): void {
        $audio = $this->ffmpeg->open($rawFile);
        $audio->filters()->clip(
            TimeCode::fromSeconds($startTime),
            TimeCode::fromSeconds($duration)
        );

        $format = new Mp3();
        $format->setAudioChannels(2)
            ->setAudioKiloBitrate(32);

        $audio->save($format, $outputPart);
    }
}
