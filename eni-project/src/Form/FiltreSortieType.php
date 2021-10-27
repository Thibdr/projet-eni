<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Security\Core\Security;

use App\Entity\Sortie;
use App\Entity\Campus;
use App\Repository\CampusRepository;

class FiltreSortieType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('site', EntityType::class, [
            'class' => Campus::class,
            'choice_label' => 'nom',
            'label' => 'Site :',
            'required'   => false,
        ]);

        $builder->add('nom', TextType::class, [
            'label' => 'Le nom de la sortie contient :',
            'required' => false,
        ]);

        $builder->add('orga', CheckboxType::class, [
            'label'    => 'Sorties dont je suis l\'organisateur/trice',
            'required' => false,
        ]);

        $builder->add('inscrit', CheckboxType::class, [
            'label'    => 'Sorties auxquelles je suis inscrit/e',
            'required' => false,
        ]);

        $builder->add('non_inscrit', CheckboxType::class, [
            'label'    => 'Sorties auxquelles je ne suis pas inscrit/e',
            'required' => false,
        ]);

        $builder->add('passees', CheckboxType::class, [
            'label'    => 'Sorties passées',
            'required' => false,
        ]);

        $builder->add('start', DateTimeType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            'constraints' => [
                new Type(['type' => '\DateTimeInterface'])
            ],
            'label' => 'Entre',
            'label_attr' => [
                'class' => 'col-form-label'
            ],
            'widget' => 'single_text',
            'required' => false,
        ]);
        
        $builder->add('end', DateType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            'constraints' => [
                new Type(['type' => '\DateTimeInterface'])
            ],
            'label' => "et",

            'label_attr' => [
                'class' => 'col-form-label'
            ],
            'widget' => 'single_text',
            'required' => false,
        ]);
    }
}
