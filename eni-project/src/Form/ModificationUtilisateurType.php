<?php

namespace App\Form;

use App\Entity\Participant;
use PHPUnit\Framework\Constraint\IsEqual;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\ZeroComparisonConstraintTrait;

class ModificationUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
                new Length([
                    'min' => 4,
                    'max' => 20,
                    'minMessage' => "Pseudo trop court ! Au moins 4 caractères !",
                    'maxMessage' => "Pseudo trop long ! Au plus 20 caractères"
                ])
            ], 'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            //->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'first_options' => [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control'
                    ],
                    'label' => 'Confirmation du mot de passe',
                ],
                'invalid_message' => 'Les champs ne correspondent pas'
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
            ])

            ->add('nom', TextType::class, [
        'constraints' => [
            new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
            new Regex([
                'pattern' => '/^[a-zA-Z]+$/',
                'message' => 'Le nom doit être au bon format'
            ]),
            new Length([
                'min' => 3,
                'max' => 50,
                'minMessage' => "Nom trop court ! Au moins 3 caractères !",
                'maxMessage' => "Nom trop long ! Au plus 50 caractères"
            ])
        ], 'label_attr' => [
            'class' => 'col-form-label'
        ]
    ])
            ->add('prenom', TextType::class, [
        'constraints' => [
            new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
            new Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le prénom doit être au bon format'
            ]),
            new Length([
                'min' => 3,
                'max' => 50,
                'minMessage' => "Prénom trop court ! Au moins 3 caractères !",
                'maxMessage' => "Prénom trop long ! Au plus 50 caractères"
            ])
        ], 'label_attr' => [
            'class' => 'col-form-label'
        ]
    ])
            ->add('telephone', TelType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un numéro de téléphone']),
                    new Regex([
                        'pattern' => '/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/',
                        'message' => 'Le numéro de téléphone doit être au bon format'
                    ])
                ],
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une adresse mail']),
                    new Regex([
                        'pattern' => '/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}$/',
                        'message' => 'L\'adresse mail doit être au bon format'
                    ])
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir une image au bon format (PNG, JPG, JPEG)',
                        'maxSize' => '10M',
                        'maxSizeMessage' => 'Taille maximal dépassée',
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
