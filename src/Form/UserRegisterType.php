<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('email', EmailType::class, [
            'help' => 'A verification link will be sent to this email',
          ])
          ->add('username', TextType::class, [
            'help' => 'Your username must be between 5 and 25 characters',
          ])
          ->add('plainPassword', RepeatedType::class, [
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
          ->add('signup', SubmitType::class, [
            'label' => 'Sign Up',
            'attr' => ['class' => 'btn-primary'],
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => User::class,
        ]);
    }
}
