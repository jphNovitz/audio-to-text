<?php

namespace App\Controller\Whisper;

use App\Form\AudioFileType;
use App\Message\AudioSplitMessage;
use App\Message\AudioToTextMessage;
use App\Message\ConvertAudioMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class WhisperController extends AbstractController
{

    #[Route('/app', name: "app_index")]
    public function index(): Response
    {
     return $this->render('whisper/index.html.twig');
    }
}
