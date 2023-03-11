<?php

namespace App\Form;

use App\Entity\WineGame;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UpdateWineGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wineGameName')
            ->add('music', ChoiceType::class, [
                'choices'  => [
                    'Musique 1' => '0',
                    'Musique 2' => '1',
                    'Musique 3' => '2',
                    'Musique 4' => '3',
                    'Musique 5' => '4',
                    "Musique 6" => '5'
                ],
            ])
            ->add('temperature', NumberType::class, [
                'scale' => 2,
            ])
            ->add('bottleCode', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 4,
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Please enter a number.'
                    ])
                ]
            ])
            ->add('userCodeName')
            ->add('userCode')
            ->add('adminCode')
            ->add('hint')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WineGame::class,
        ]);
    }
}
