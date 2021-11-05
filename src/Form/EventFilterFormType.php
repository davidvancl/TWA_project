<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $hide = [];
        if (!$options['admin']) {
            $hide = [
                'hidden' => 'true'
            ];
        }

        $builder
            ->add('show_finished', CheckboxType::class, [
                'required' => false,
            ])
            ->add('only_after_term', CheckboxType::class, [
                'required' => false,
            ])
            ->add('only_before_term', CheckboxType::class, [
                'required' => false,
            ])
            ->add('show_main_page', CheckboxType::class, [
                'required' => false,
                'attr' => $hide
            ])
            ->add('sort', ChoiceType::class, [
                'choices'  => [
                    "Seřadit dle:" => 'none',
                    "Datum vytvoření" => 'date_created',
                    "Termín odevzdání" => 'date_to'
                ],
                'attr' => [
                    'class' => 'form-select form-select-sm'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'admin' => false
        ]);
    }
}