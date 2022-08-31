<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateType::class, [
                'widget' => 'single_text',
                'required'=>false
            ])
            ->add('duree')
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
                'required'=>false
            ])
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom',
                "attr" => [
                "class" => "uk-select"
                ],
            ])
            ->add('lieu',EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                "attr" => [
                "class" => "uk-select"
                ],
            ])

            ->add('Annuler',ResetType::class,[
                'attr' => ['class' => 'save'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
