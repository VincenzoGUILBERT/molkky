<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserType extends AbstractType
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('name', null, [
                'label' => 'Nom'
            ])
            ->add('surname', null, [
                'label' => 'Prénom'
            ])
            ->add('password', null, [
                'label' => 'Mot de passe'
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->setHashedPassword(...))

        ;
    }

    public function setHashedPassword(FormEvent $formEvent): void
    {
        $user = $formEvent->getData();

        if ($user->getId() === null) {

            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
