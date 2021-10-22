<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class LieuType extends AbstractType
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
                        'min' => 5,
                        'max' => 80,
                        'minMessage' => "Nom trop court ! Au moins 5 caractères !",
                        'maxMessage' => "Nom trop long ! Au plus 80 caractères"
                    ])
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('rue', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un nom de rue']),
                    new Length([
                        'min' => 5,
                        'max' => 150,
                        'minMessage' => "Nom trop court ! Au moins 5 caractères !",
                        'maxMessage' => "Nom trop long ! Au plus 150 caractères"
                    ])
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('latitude',NumberType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)$/',
                        'message' => 'La latitude doit être un nombre décimal ou entier'
                    ])
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('longitude',NumberType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)$/',
                        'message' => 'La longitude doit être un nombre décimal ou entier'
                    ])
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('ville', EntityType::class, [
                'attr' => [
                    'class' => 'form-select'
                ],
                'choice_label' => function($ville) {
                    return $ville->getCodePostal() . ' ' . $ville->getNom();
                },
                'class' => Ville::class,
                'constraints' => [
                    new Type(['type' => 'App\Entity\Ville']),
                ],
                'empty_data'  => null,
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'placeholder' => 'Choisir une ville'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
