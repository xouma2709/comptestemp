<?php

namespace App\Form;

use App\Entity\Comptes;
use App\Entity\Fonctions;
use App\Entity\Softs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenererComptesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Fonction', EntityType::class,[
                'class' => Fonctions::class,
                'choice_label' => 'LibelleFonction',
                'multiple' => false,
                'required' => true
            ])
            ->add('Soft', EntityType::class,[
                'class' => Softs::class,
                'choice_label' => 'Libellesoft',
                'multiple' => false,
                'required' => true
            ])
            ->add('Generer_10_comptes', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comptes::class,
        ]);
    }
}
