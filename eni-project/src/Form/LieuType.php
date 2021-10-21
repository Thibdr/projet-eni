<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('rue')
            ->add('latitude')
            ->add('longitude')
            ->add('ville', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'choice_label' => function($ville) {
                    return $ville->getCodePostal() . ' ' . $ville->getNom();
                },
                'class' => Ville::class,
                'constraints' => [
                    new Type(['type' => 'App\Entity\Ville']),
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
            'data_class' => Lieu::class,
        ]);
    }
}
