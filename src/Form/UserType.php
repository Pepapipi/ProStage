<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('email')
            ->add('password', RepeatedType::class,
            [
                'type' => PasswordType::class,
                'invalid_message' =>  'Les mots de passe saisies ne correspondent pas',
                'options' => ['attr' =>  ['class' => 'password-field']],
                'required'=> true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Repetez le mot de passe'],
            ])  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}