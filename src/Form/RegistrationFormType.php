<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, $this->getDefaultClass())
            ->add('email', TextType::class, $this->getDefaultClass())
            ->add('surname', TextType::class, $this->getDefaultClass())
            ->add('gender', ChoiceType::class, [
                'choices'  => [
                    'Neuvedeno' => 'none',
                    'Muž' => 'man',
                    'Žena' => 'woman',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control rounded-4'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Zadej své nové heslo',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Tvoje heslo musí obsahovat minimálně {{ limit }} znaků',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    private function getDefaultClass() {
        return [
            'attr' => [
                'class' => 'form-control rounded-4'
            ],
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
