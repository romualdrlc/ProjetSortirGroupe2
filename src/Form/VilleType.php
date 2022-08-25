<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',null,[
                "label_attr" => [
                    "class" => "uk-form-label",
                    "for" => "form-horizontal-text"
                ],
                "attr" => [
                    "class" => "uk-input",
                    "id" => "form-horizontal-text",
                    "type" => "text",

                ],
            ])
            ->add('codePostal',null,[
                "label_attr" => [
                    "class" => "uk-form-label",
                    "for" => "form-horizontal-text"
                ],
                "attr" => [
                    "class" => "uk-input",
                    "id" => "form-horizontal-text",
                    "type" => "text",

                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'token',
        ]);
    }
}
