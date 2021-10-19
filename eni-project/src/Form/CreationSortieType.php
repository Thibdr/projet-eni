<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class CreationSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un nom']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => "Nom trop court ! Au moins 3 caractères !",
                        'maxMessage' => "Nom trop long ! Au plus 255 caractères"
                    ])
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une date de début']),
                    new Type(['type' => '\DateTimeInterface'])
                ],
                'label' => 'Date et heure de la sortie',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => "Veuillez renseigner une date limite d'inscription"]),
                    new Type(['type' => '\DateTimeInterface'])
                ],
                'label' => "Date limite d'inscription",
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => "Veuillez renseigner un nombre de places maximum"]),
                    new Positive()
                ],
                'label' =>  'Nombre de places',
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('duree', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une durée']),
                    new Positive()
                ],
                'label' => 'Durée',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
            ])
            ->add('informations', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Description et infos',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'required' => false
            ])
            ->add('lieu', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'choice_label' => 'nom',
                'class' => Lieu::class,
                'constraints' => [
                    new Type(['type' => 'App\Entity\Lieu']),
                ],
                'label' => 'Lieu',
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
