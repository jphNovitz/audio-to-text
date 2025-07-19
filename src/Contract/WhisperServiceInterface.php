<?php

namespace App\Contract;

interface WhisperServiceInterface
{
    /**
     * Transcrit un dossier d'audio en texte via l'API Whisper
     *
     * @param string $audioDirPath Chemin vers le dossier contenant les segments audio
     * @return bool True si la transcription s'est bien passée
     * @throws \Exception Si les fichiers ne sont pas trouvés ou en cas d'erreur API
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function transcribeAudio(string $audioDirPath = "audio_segments"): bool;
} 