<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('password')
            ->add('roles', ChoiceType::class,[
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->getCivility(),
                'label' => 'Role',
            ])
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
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
                ]
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

    private function getCivility() : array {
        return [
            'Utilisateur' => "ROLE_USER",
            'Administrateur' => "ROLE_ADMIN"
        ];
    }
}
