<?php

namespace App\Form;

use App\Entity\Agents;
use App\Entity\Fonctions;
use App\Entity\Softs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class, ['required' => true])
            ->add('Prenom', TextType::class, ['required' => true])
            ->add('DateDebut', DateType::class, [
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('DateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('Secteur', TextType::class, ['required' => false])
            ->add('Softs', EntityType::class,[
                'class' => Softs::class,
                'choice_label' => 'Libellesoft',
                'multiple' => false,
                'required' => true
            ])
            ->add('Fonction', EntityType::class,[
                'class' => Fonctions::class,
                'choice_label' => 'LibelleFonction',
                'multiple' => false,
                'required' => true
            ])
            ->add('Ajouter', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agents::class,
        ]);
    }
}
