<?php

namespace App\Twig\Components;

use App\Message\AudioSplitMessage;
use App\Message\AudioToTextMessage;
use App\Service\AudioConverterService;
use App\Service\MessagePublisher;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;


#[AsLiveComponent]
final class UploadAudio
{
    use DefaultActionTrait;

    public function __construct(
        private ValidatorInterface             $validator,
        #[Autowire('%upload_directory%')]
        private readonly string                $uploadDirectory,
        private readonly MessageBusInterface   $bus,
        private readonly MessagePublisher $messagePublisher,
        private readonly AudioConverterService $audioConverterService,
    )
    {
    }

    #[LiveProp(writable: true)]
    public $audioFilename = null;

    #[LiveProp]
    public $error = null;

    #[LiveAction]
    public function uploadFiles(Request $request): void
    {
        // Réinitialiser l'erreur
        $this->error = null;

        $file = $request->files->get('audioFile');

        // Vérifier si un fichier a été uploadé
        if (!$file instanceof UploadedFile) {
            $this->error = 'Aucun fichier sélectionné';
            return;
        }

        // Valider le fichier
        $this->validateFile($file);

        // Si pas d'erreur, traiter le fichier
        if ($this->error === null) {
            if ($this->audioFilename = $this->copyFile($file)) {
                $this->process();
            }
        }

//        $this->process($this->audioFilename);
    }

    private function copyFile(UploadedFile $file): string
    {

        $destination = $this->uploadDirectory;

        // Nom du fichier de sortie
        $audioFilename = 'input.' . $file->getClientOriginalExtension();
        $file->move($destination, $audioFilename);

        return $audioFilename;
    }

    public function process(): void
    {
       $output = $this->audioConverterService->convertToMp3($this->audioFilename);

         if ($output !== false) {
           $this->messagePublisher->publishProgress(
               progression:true,
               status: 'fichier converti');

          $this->bus->dispatch(new AudioSplitMessage(inputFile: $output));
           $this->bus->dispatch(new AudioToTextMessage());
       }



    }

    private function validateFile(UploadedFile $singleFileUpload): void
    {
        $errors = $this->validator->validate($singleFileUpload, [
            new Assert\File([
                'maxSize' => '100M',
                'mimeTypes' => [
                    'audio/mpeg',
                    'audio/wav',
                    'audio/mp3',
                    'audio/ogg',
                    'audio/flac'
                ],
                'mimeTypesMessage' => 'Veuillez uploader un fichier audio valide',
            ]),
        ]);

        if (count($errors) === 0) {
            return;
        }

        $this->error = $errors->get(0)->getMessage();

    }
}
