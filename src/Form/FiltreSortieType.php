<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'required'=>false
            ])
            ->add('nomSortie',EntityType::class,[
                'class'=>Sortie::class,
                'choice_label'=>'nom',
                'required'=>false
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'required'=>false
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required'=>false
            ])
            ->add('public', ChoiceType::class, [
                'multiple' => true,
                'label'=> false,
                'choices'  => [
                    "Sorties dont je suis l'oganisateur" => 1,
                    "Sorties auxquelles je suis inscrite" => 2,
                    "Sorties auxquelles je ne suis pas inscrite" => 3,
                    "Sorties passées" => 4,
                ],
                'expanded' => true,
                'required'=>false
            ])
            ->add('Rechercher', SubmitType::class, [
                'attr' => ['class' => 'Rechercher'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
