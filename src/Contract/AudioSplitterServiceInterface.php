<?php

namespace App\Contract;

interface AudioSplitterServiceInterface
{
    /**
     * Découpe un fichier audio en segments
     *
     * @param string $inputFile Chemin vers le fichier audio à découper
     * @param string $outputDir Dossier où stocker les segments
     * @return bool True si le découpage s'est bien passé, false sinon
     * @throws \RuntimeException Si le fichier source n'existe pas
     */
    public function split(string $inputFile, string $outputDir = "audio_segments"): bool;
} 