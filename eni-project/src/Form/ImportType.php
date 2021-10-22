<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class ImportType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('donnees',FileType::class,[
            'label' => 'Fichier à importer * : ',
            'help' => 'Attention: votre fichier doit être au format .csv',
            'mapped' => false,
            'required' => true,
            'attr' => [
                'lang' => 'fr'
            ]
        ])
        ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $form->add('donnees',FileType::class,[
                'label' => 'Fichier à importer *',
                'help' => 'Attention: votre fichier doit être au format .csv',
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'lang' => 'fr'
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain'
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un document au bon format',
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Taille maximal dépassée',
                    ]),
                ]
            ]);
        });
    }
}
