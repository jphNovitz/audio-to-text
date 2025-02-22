<?php

namespace App\Controller\Whisper;

use App\Form\AudioFileType;
use App\Message\AudioSplitMessage;
use App\Message\AudioToTextMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class WhisperController extends AbstractController
{

    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'app_default')]
    public function upload(Request $request, MessageBusInterface $bus, HubInterface $hub): Response
    {
        $form = $this->createForm(AudioFileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                // Récupère le chemin de destination depuis les paramètres
                $destination = $this->getParameter('upload_directory');
                // Nom fixe pour le fichier uploadé (attention : il sera écrasé à chaque upload)
                $nomFichier = 'input.' . $file->getClientOriginalExtension();

                $file->move($destination, $nomFichier);
                $bus->dispatch(new AudioSplitMessage());
                $bus->dispatch(new AudioToTextMessage());
                return $this->render('whisper/process.html.twig');
            }
        }

        return $this->render('whisper/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
