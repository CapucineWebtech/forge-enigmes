<?php

namespace App\Form;

use App\Entity\WineGame;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class UpdateWineGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wineGameName')
            ->add('padlockIsOpen', CheckboxType::class, [
                'label' => 'Cadenas ouvert',
                'mapped' => false,
                'required' => false,
                'data' => $options['wineGame']->isPadlockIsOpen()
            ])
            ->add('music', ChoiceType::class, [
                'choices'  => [
                    'Musique 1' => '0',
                    'Musique 2' => '1',
                    'Musique 3' => '2',
                    'Musique 4' => '3',
                    'Musique 5' => '4',
                    "Musique 6" => '5'
                ],
                'mapped' => false,
                'data' => $options['wineGame']->getMusic()
            ])
            ->add('temperature', NumberType::class, [
                'scale' => 2,
                'constraints' => [
                    new Range([
                        'min' => 10,
                        'max' => 25,
                        'notInRangeMessage' => 'La température doit être entre {{ min }} et {{ max }}'
                    ])
                ],
                'mapped' => false,
                'data' => $options['wineGame']->getTemperature()
            ])
            ->add('bottleCode', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Veuillez remplir 4 caractères',
                        'max' => 4,
                        'maxMessage' => 'Veuillez remplir 4 caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Veuillez remplir des nombres'
                    ])
                ],
                'mapped' => false,
                'data' => $options['wineGame']->getBottleCode()
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
            'wineGame' => null,
        ]);
    }
}
