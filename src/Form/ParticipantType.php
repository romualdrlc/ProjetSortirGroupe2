<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('administrateur')
            ->add('actif')
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom',
            ])
            ->add('sorties',EntityType::class,[
                'class'=>Sortie::class,
                'choice_label'=>'nom',
                'multiple'=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
