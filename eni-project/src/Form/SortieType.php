<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SortieType extends AbstractType
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
                'label' => 'Nombre de places',
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
            /*
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
            */
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'placeholder' => 'Selectionner une ville',
                'required' => false
            ])
            ->add('nomLieu', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('rue', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('latitude', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('longitude', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
            ])
            ->add('publish', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Publier'
            ]);


        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                $form = $event->getForm();

                $this->addPlaceField($form->getParent(), $form->getData());
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function(FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                $lieu = $data->getLieu();
                if($lieu) {
                    $ville = $lieu->getVille();
                    $this->addPlaceField($form, $ville);
                    $form->get('ville')->setData($ville);
                } else {
                    $this->addPlaceField($form, null);
                }
            }
        );
    }

    private function addPlaceField(FormInterface $form, ?Ville $ville) {
        $form->add('lieu', EntityType::class, [
            'class' => Lieu::class,
            'choices' => $ville ? $ville->getLieux() : [],
            'placeholder' => $ville ? 'Selectionner un lieu' : 'Selectionner une ville',
            'required' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
