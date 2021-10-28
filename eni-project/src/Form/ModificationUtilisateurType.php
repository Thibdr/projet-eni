<?php

namespace App\Form;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use PHPUnit\Framework\Constraint\IsEqual;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\ZeroComparisonConstraintTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ModificationUtilisateurType extends AbstractType
{
    private $em;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @internal
     * @required
     */
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
    }
    /**
     * Get a user from the Security Token Storage.
     *
     * @return UserInterface|object|null
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->em = $participantRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Veuillez renseigner un pseudo']),
                new Callback([
                    $this, 'validate'
                ]),
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
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control'
                    ],
                    'label' => 'Confirmation du nouveau mot de passe',
                ],
                'invalid_message' => 'Les champs ne correspondent pas'
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
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
        ]
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

    public function validate($pseudo, ExecutionContextInterface $context): void
    {
        $user = $this->getUser();
        $pseudoexist = $this->em->findExistPseudo($pseudo);
        if($pseudoexist != null && $user->getPseudo() != $pseudoexist[0]["pseudo"]){
            $context->buildViolation('Le pseudo est déjà utilisé, veuillez en choisir un autre')
                ->atPath('pseudo')
                ->addViolation();
        }
    }
}
