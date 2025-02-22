<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AudioFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label' => 'Télécharger le fichier',
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '204800k',
                    'mimeTypes' => [
                        'audio/m4a',
                        'audio/mpeg',
                        'audio/mp3',
                        'audio/wav',
                        'audio/x-wav',
                        'audio/wave',
                    ]
                ]),
                new NotBlank([
                    'message' => 'Veuillez sélectionner un fichier audio',
                ]),
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
