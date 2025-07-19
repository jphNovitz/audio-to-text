<?php

namespace App\Contract;

interface AudioConverterServiceInterface
{
    /**
     * Convertit un fichier audio en format MP3
     *
     * @param string $filename Nom du fichier à convertir
     * @return string|bool Le chemin du fichier converti ou false en cas d'échec
     */
    public function convertToMp3(string $filename): string|bool;
} 