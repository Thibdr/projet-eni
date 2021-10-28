<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un nom']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => "Nom trop court ! Au moins 3 caractères !",
                        'maxMessage' => "Nom trop long ! Au plus 255 caractères"
                    ])
                ],
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'by_reference' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une date de début']),
                    new Type(['type' => '\DateTimeInterface'])
                ],
                'label' => 'Date et heure de la sortie',
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'by_reference' => true,
                'constraints' => [
                    new NotBlank(['message' => "Veuillez renseigner une date limite d'inscription"]),
                    new Type(['type' => '\DateTimeInterface'])
                ],
                'label' => "Date limite d'inscription",
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => "Veuillez renseigner un nombre de places maximum"]),
                    new Positive(['message' => "Le nombre de places doit être supérieur à 0"])
                ],
                'label' => 'Nombre de places',
            ])
            ->add('duree', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une durée']),
                    new Positive(['message' => "Le nombre de places doit être supérieur à 0"])
                ],
                'label' => 'Durée',
            ])
            ->add('informations', TextareaType::class, [
                'label' => 'Description et infos',
                'required' => false
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'constraints' => [
                    new NotBlank(['message' => "Veuillez choisir une ville"]),
                ],
                'mapped' => false,
                'placeholder' => 'Selectionner une ville',
                'required' => false,
            ])
            ->add('nomLieu', TextType::class, [
                'label' => 'Nom',
                'mapped' => false,
                'required' => false
            ])
            ->add('rue', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('latitude', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('longitude', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-warning'
                ],
            ])
            ->add('publish', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-warning'
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
                    $form->get('ville')->setData($ville);

                    $this->addPlaceField($form, $ville);
                } else {
                    $this->addPlaceField($form, null);
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                $data->nomLieu = $form->get('nomLieu')->getData();
                $data->rue = $form->get('rue')->getData();
                $data->latitude = $form->get('latitude')->getData();
                $data->longitude = $form->get('longitude')->getData();
                $this->validate($form, $data);
            }
        );
    }

    public function validate(Form $form, object $data): void
    {
        if($data->getLieu() == null && $data->nomLieu == null) {
            $error = new FormError('Vous devez définir un lieu pour la sortie');

            $form->get('lieu')->addError($error);
            $form->get('nomLieu')->addError($error);
        } else if($data->getLieu() != null && $data->nomLieu != null) {
            $error = new FormError('Vous ne pouvez pas devez définir plusieurs fois le lieu pour la sortie');

            $form->get('lieu')->addError($error);
            $form->get('nomLieu')->addError($error);
        }
        if($data->nomLieu != null) {
            if($data->rue == null) {
                $form->get('rue')->addError(new FormError('Veuillez renseigner un nom de rue'));
            }
            if($data->latitude == null) {
                $form->get('latitude')->addError(new FormError('Veuillez renseigner une latitude'));
            } else if(!is_numeric($data->latitude)) {
                $form->get('latitude')->addError(new FormError('La longitude doit être un nombre décimal'));
            }
            if($data->longitude == null) {
                $form->get('longitude')->addError(new FormError('Veuillez renseigner une longitude'));
            } else if(!is_numeric($data->longitude)) {
                $form->get('longitude')->addError(new FormError('La longitude doit être un nombre décimal'));
            }
        }
    }

    private function addPlaceField(FormInterface $form, ?Ville $ville) {
        $form->add('lieu', EntityType::class, [
            'class' => Lieu::class,
            'choices' => $ville ? $ville->getLieux() : [],
            'placeholder' => 'Selectionner un lieu',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
