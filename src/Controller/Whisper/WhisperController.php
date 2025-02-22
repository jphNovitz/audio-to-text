<?php

namespace App\Controller\Whisper;

use App\Form\AudioFileType;
use App\Message\AudioSplitMessage;
use App\Message\AudioToTextMessage;
use App\Message\ConvertAudioMessage;
use App\Service\AudioConverterService;
use App\Service\AudioSplitterService;
use App\Service\WhisperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class WhisperController extends AbstractController
{

    #[Route('/', name: 'app_default')]
    public function upload(Request $request, MessageBusInterface $bus,HubInterface $hub): Response
    {
        $form = $this->createForm(AudioFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                // Récupère le chemin de destination depuis les paramètres (voir ci-dessous)
                $destination = $this->getParameter('upload_directory');
                // Nom fixe pour le fichier uploadé (attention : il sera écrasé à chaque upload)
                $nomFichier = 'input.' . $file->getClientOriginalExtension();

                try {
                    $file->move($destination, $nomFichier);
//                    return $this->redirectToRoute('app_conversion');
                    $bus->dispatch(new ConvertAudioMessage());
                    $bus->dispatch(new AudioSplitMessage());
                    $bus->dispatch(new AudioToTextMessage());
//die;
                    return $this->render('whisper/process.html.twig');
                } catch (FileException $e) {
                    dd('erreur upload');
                    // Traiter l'erreur si nécessaire
                }

            }
        }

        return $this->render('whisper/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/conversion', name: 'app_conversion')]
    public function transcribeAudio(MessageBusInterface $bus,AudioSplitterService $audioSplitterService,  AudioConverterService $audioConverterService, WhisperService $whisperService): Response
    {
//        $audioConverterService->convertToMp3();
//        $audioSplitterService->split();
//        $whisperService->transcribeAudio();
        $bus->dispatch(new ConvertAudioMessage());
        $bus->dispatch(new AudioSplitMessage());
        $bus->dispatch(new AudioToTextMessage());
//die;
        return $this->render('whisper/process.html.twig');
    }
}
