<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AddEventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getDefaultSettings())
            ->add('dateTo', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('body', TextareaType::class, $this->getDefaultSettings())
            ->add('hrefText', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ],
                'required' => false,
            ])
            ->add('href', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ],
                'required' => false,
            ])
            ->add('imagePath', ChoiceType::class, [
                'choices'  => $options['images'],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('visibility', ChoiceType::class,[
                'choices'  => [
                    'Veřejné' => 'public',
                    'Privátní' => 'private',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('section', ChoiceType::class, [
                'choices'  => [
                    'Osobní' => 'personal',
                    'Hlavní stránka' => 'main_page',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    private function getDefaultSettings() {
        return [
            'attr' => [
                'class' => 'form-control',
                'autocomplete' => 'off'
            ]
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'images' => ['default' => 'default']
        ]);
    }
}