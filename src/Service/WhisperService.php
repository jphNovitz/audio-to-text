<?php

namespace App\Service;

use CURLFile;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function PHPUnit\Framework\directoryExists;

class WhisperService
{
    private $apiKey;
    private $httpClient;

    public function __construct(string $apiKey, HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function transcribeAudio(string $audioDirPath = "audio_segments"): string
    {
        $projectRoot = __DIR__ . '/../../';
        $tempPath = Path::normalize($projectRoot . 'var/tmp/' . $audioDirPath);

        if (!directoryExists($tempPath)) {
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

        $transcript = '';

        foreach ($audioParts as $index => $audioPart) {
            $audioFilePath = $tempPath . '/' . $audioPart;
//            dump($audioFilePath);
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/audio/transcriptions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'body' => [
                    'file' => fopen($audioFilePath, 'r'),
                    'model' => 'whisper-1',
                    'language' => 'fr', // Facultatif, auto-détection sinon
                    'temperature' => '0' // Facultatif
                ]
            ]);

            // Récupérer la réponse
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $data = $response->toArray();
//                dump($data['text']);
//                $transcript .= " " . $data['text'];
                $transcript_array[$index] = $data['text'];

            }
//            else {
//                throw new \Exception("Erreur API Whisper : " . $response->getContent(false));
//            }
        }

        //// Initialise cURL
        //        $ch = curl_init();
        //
        //        curl_setopt_array($ch, [
        //            CURLOPT_URL => 'https://api.openai.com/v1/audio/transcriptions',
        //            CURLOPT_RETURNTRANSFER => true,
        //            CURLOPT_POST => true,
        //            CURLOPT_POSTFIELDS => [
        //                // Envoie le fichier audio en utilisant CURLFile
        //                'file' => new CURLFile($audioFilePath),
        //                // Précise le modèle à utiliser
        //                'model' => 'whisper-1',
        //                // Tu peux ajouter d'autres paramètres optionnels si besoin, par ex. 'language'
        //            ],
        //            CURLOPT_HTTPHEADER => [
        //                'Authorization: Bearer ' . $this->apiKey,
        //            ],
        //        ]);
        //
        //        $response = curl_exec($ch);
        //
        //        if (curl_errno($ch)) {
        //            echo 'Erreur cURL : ' . curl_error($ch);
        //        } else {
        //            return $response;
        //        }
        //
        //        curl_close($ch);
        //


        ksort($transcript_array);

        return implode(" ", $transcript_array);
    }
}
