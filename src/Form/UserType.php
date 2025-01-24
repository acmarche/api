<?php

namespace AcMarche\Api\Form;

use AcMarche\Api\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = ['ROLE_API_API' => 'ROLE_API_API', 'ROLE_API_ADMIN' => 'ROLE_API_ADMIN'];
        $builder
            ->add('nom')
            ->add('username')
            ->add('email')
            ->add('password')
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => $roles,
                    'required' => true,
                    'multiple' => true,
                    'expanded' => true,
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ],
        );
    }
}
