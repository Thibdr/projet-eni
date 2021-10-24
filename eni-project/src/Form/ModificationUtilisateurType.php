<?php

namespace App\Form;

use App\Entity\Participant;
use PHPUnit\Framework\Constraint\IsEqual;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;

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
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un mot de passe']),
                ]
            ])
            ->add('nom', TextType::class, [
        'constraints' => [
            new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
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
            ->add('telephone', TelType::class)
            ->add('mail', EmailType::class)
            ->add('photo', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
