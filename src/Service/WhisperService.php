<?php

namespace App\Service;

use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function PHPUnit\Framework\directoryExists;

readonly class WhisperService
{

    public function __construct(private string $apiKey, private string $baseDir, private HttpClientInterface $httpClient, private HubInterface $hub)
    {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function transcribeAudio(string $audioDirPath = "audio_segments"): bool
    {
        $tempPath = Path::normalize($this->baseDir . '/var/tmp/' . $audioDirPath);

        if (!is_dir($tempPath)) {
            throw new \Exception("Fichiers non trouvé : " . $tempPath);
        }

        $finder = new Finder();
        $audioParts = [];
        $finder->files()
            ->in($tempPath)
            ->name('segment_*.mp3')
            ->sortByName();

        foreach ($finder as $file) {
            $audioParts[] = $file->getRelativePathname();
        }


        foreach ($audioParts as $index => $audioPart) {
            $audioFilePath = $tempPath . '/' . $audioPart;
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/audio/transcriptions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'body' => [
                    'file' => fopen($audioFilePath, 'r'),
                    'model' => 'whisper-1',
                    'language' => 'fr',
                    'prompt' => $index === 0
                        ? "Clean transcription without repetitions."
                        : "Continue the transcription naturally, without repeating previous content.",
                    'temperature' => '0'
                ]
            ]);

            // Récupérer la réponse
            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $data = $response->toArray();
                dump($data['text']);
                $message = str_replace('. ', '.<br>', trim($data['text']));

                try {
                    $update = new Update(
                        'transcription',
                        json_encode(['message' => $message, 'segment' => $index])
                    );

                    $this->hub->publish($update);
                } catch (\Exception $e) {
                    throw new \Exception("Erreur Mercure : " . $e->getMessage());
                }
            } else {
                throw new \Exception("Erreur API Whisper : " . $response->getContent(false));
            }
        }

        return true;
    }
}
