<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserNewPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The passwords must match.',
            'first_options' => [
              'label' => 'Password',
              'help' => 'Your password must be at least 8 characters long',
            ],
            'second_options' => [
              'label' => 'Repeat Password',
              'help' => 'Passwords must match',
            ],
          ])
          ->add('submit', SubmitType::class, [
            'label' => 'Change Password',
            'attr' => ['class' => 'btn-primary'],
          ]);
    }
}