<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Entity\Sortie;
use App\Entity\Lieu;


class FiltreSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('site', EntityType::class, [
            'class' => Lieu::class,
            'choice_label' => 'nom',
            'label' => 'Site :',
            'required'   => false,
            'empty_data' => null,
            'attr' => [
                'placeholder' => null,
                //'class' => 'selectpicker'
            ],
        ]);

        $builder->add('nom', TextType::class, [
            'label' => 'Le nom de la sortie contient :',
            'required' => false,
        ]);

        $builder->add('orga', CheckboxType::class, [
            'label'    => 'Sorties dont je suis l\'organsateur/trice',
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

        $builder->add('start', TextType::class, [
            'label' => 'Entre',
            'required' => false,
            'attr' => [
                'class' => 'datepicker',
            ],
        ]);
        
        $builder->add('end', TextType::class, [
            'label' => 'et :',
            'required' => false,
            'attr' => [
                'class' => 'datepicker',
            ],
        ]);
    }
}
