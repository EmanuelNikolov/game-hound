<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserResetPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('login_credential', TextType::class, [
            'constraints' => new NotBlank(),
            'label' => 'Username or Email',
            'help' => 'A verification link will be sent to this email',
          ])
          ->add('submit', SubmitType::class, [
            'label' => 'Reset Password',
            'attr' => ['class' => 'btn-primary'],
          ]);
    }
}