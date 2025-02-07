<?php

namespace App\Controller;

use App\Service\AudioConverterService;
use App\Service\AudioSplitterService;
use App\Service\WhisperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WhisperController extends AbstractController
{
    #[Route('/whisper', name: 'app_whisper')]
    public function transcribeAudio(AudioSplitterService $audioSplitterService, AudioConverterService $audioConverterService, WhisperService $whisperService): Response
    {
        $audioConverterService->convertToMp3(
            $this->getParameter('kernel.project_dir') . '/public/_voix 008_sd.m4a',
            $this->getParameter('kernel.project_dir') . '/public/full_raw.mp3'
        );

        $audioSplitterService->split(
            $this->getParameter('kernel.project_dir') . '/public/full_raw.mp3',
        );

     /*   // Récupérer le répertoire du projet
        $projectDir = $this->getParameter('kernel.project_dir');
        // Construire le chemin complet vers le fichier audio situé dans le dossier public
        $filePath = $projectDir . '/public/essai.mp3';
//        $filePath = $projectDir . '/public/Essai.ogg';
//        $filePath = $projectDir . '/public/apollo.ogg';*/


        try {
            $transcription = $whisperService->transcribeAudio();
            return $this->render('whisper/index.html.twig', [
                'transcription' => $transcription,
            ]);
        } catch (\Exception $e) {
            dd($e);
            return new Response('Erreur : ' . $e->getMessage());
        }
    }
}
