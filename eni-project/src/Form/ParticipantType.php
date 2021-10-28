<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9_]+$/',
                        'message' => 'Le pseudo doit être au bon format'
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 20,
                        'minMessage' => "Pseudo trop court ! Au moins 4 caractères !",
                        'maxMessage' => "Pseudo trop long ! Au plus 20 caractères"
                    ])
                ], 'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'label' => 'Pseudo :'
            ])
            ->add('password', PasswordType::class,[
                'required' => false,
                'empty_data' => ''
            ])
            ->add('roles', ChoiceType::class,[
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->getRole(),
                'label' => 'Role :',
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
                    new Regex([
                        'pattern' => '/^[a-zA-Zéèà]+$/',
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
                ],
                'label' => 'Nom :'
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
                    new Regex([
                        'pattern' => '/^[a-zA-Zéèà]+$/',
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
                ],
                'label' => 'Prénom :'
            ])
            ->add('telephone', TelType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un numéro de téléphone']),
                    new Regex([
                        'pattern' => '/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/',
                        'message' => 'Le numéro de téléphone doit être au bon format'
                    ])
                ],
                'label' => 'Téléphone :'
            ])
            ->add('mail', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une adresse mail']),
                    new Regex([
                        'pattern' => '/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}$/',
                        'message' => 'L\'adresse mail doit être au bon format (xx@example.com'
                    ])
                ],
                'label' => 'Mail :'
            ])
            ->add('campus', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'choice_label' => 'nom',
                'class' => Campus::class,
                'constraints' => [
                    new Type(['type' => 'App\Entity\Campus']),
                ],
                'label' => 'Campus',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'label' => 'Campus :'
            ])
            ->add('actif')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }

    private function getRole() : array {
        return [
            'Utilisateur' => "ROLE_USER",
            'Administrateur' => "ROLE_ADMIN"
        ];
    }
}
