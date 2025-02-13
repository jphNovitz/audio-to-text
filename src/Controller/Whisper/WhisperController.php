<?php

namespace App\Controller\Whisper;

use App\Form\AudioFileType;
use App\Message\AudioSplitMessage;
use App\Message\AudioToTextMessage;
use App\Message\ConvertAudioMessage;
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
    public function upload(Request $request, HubInterface $hub): Response
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
                    return $this->redirectToRoute('app_conversion');
                } catch (FileException $e) {
                    dd($e);
                    // Traiter l'erreur si nécessaire
                }

            }
        }

        return $this->render('whisper/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/conversion', name: 'app_conversion')]
    public function transcribeAudio(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new ConvertAudioMessage());
        $bus->dispatch(new AudioSplitMessage());
        sleep(120);
        $bus->dispatch(new AudioToTextMessage());

        /*   // Récupérer le répertoire du projet
           $projectDir = $this->getParameter('kernel.project_dir');
           // Construire le chemin complet vers le fichier audio situé dans le dossier public
           $filePath = $projectDir . '/public/essai.mp3';
   //        $filePath = $projectDir . '/public/Essai.ogg';
   //        $filePath = $projectDir . '/public/apollo.ogg';*/


//        try {
//            $transcription = $whisperService->transcribeAudio();
//            return $this->render('whisper/index.html.twig', [
//                'transcription' => $transcription,
//            ]);
//        } catch (\Exception $e) {
//            dd($e);
//            return new Response('Erreur : ' . $e->getMessage());
//        }
        return $this->render('whisper/process.html.twig');
    }
}
