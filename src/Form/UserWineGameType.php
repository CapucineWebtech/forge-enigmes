<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\WineGame;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserWineGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wineGame', EntityType::class, [
                'class' => WineGame::class,
                'choice_label' => function (WineGame $wineGame) {
                    return sprintf('%s - %s', $wineGame->getId(), $wineGame->getWineGameName());
                },
                'mapped' => false,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%s - %s', $user->getId(), $user->getEmail());
                },
                'mapped' => false,
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
