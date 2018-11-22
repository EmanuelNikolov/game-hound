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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGeneralType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('email', EmailType::class, [
            'help' => 'A verification link will be sent to this email',
            'required' => false,
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
            'required' => false,
          ])
          ->add('signup', SubmitType::class, [
            'label' => 'Sign Up',
            'attr' => ['class' => 'btn-primary'],
          ]);

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
          function (FormEvent $event) {
              $user = $event->getData();
              $form = $event->getForm();

              if (null !== $user->getId()) {
                  $form->remove('username');
                  $form->remove('signup');
                  $form->add('submit', SubmitType::class, [
                    'label' => 'Edit',
                    'attr' => ['class' => 'btn-primary'],
                  ]);
              }
          });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
          'data_class' => User::class,
          'validation_groups' => function (FormInterface $form) {
              $user = $form->getData();

              if (null === $user->getId()) {
                  return ['Default', 'edit'];
              }

              return ['edit'];
          },
        ]);
    }
}
