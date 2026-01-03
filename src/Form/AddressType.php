<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'label' => false,
                'constraints' => [
                    new Length(
                        max: 30,
                        maxMessage: 'L\'adresse ne peut pas dépasser {{ limit }} caractères.'
                    ),
                    new Regex(
                        pattern: '/^[0-9a-zA-ZÀ-ÿ\s\-\',\.]+$/u',
                        message: 'L\'adresse ne peut contenir que des lettres, chiffres, espaces et caractères spéciaux (- \' , .).'
                    ),
                ],
                'attr' => [
                    'maxlength' => 30,
                    'rows' => 5,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new Length(
                        max: 30,
                        maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.'
                    ),
                    new NotBlank(message: 'La ville est obligatoire.'),
                    new Regex(
                        pattern: '/^[a-zA-ZÀ-ÿ\s\-\']+$/u',
                        message: 'La ville ne peut contenir que des lettres, espaces, tirets et apostrophes.'
                    ),
                ],
                'attr' => [
                    'placeholder' => 'Saisir la Ville',
                    'maxlength' => 30,
                    'rows' => 1,
                ],
            ])
            ->add('cp', IntegerType::class, [
                'label' => 'Code Postal',
                'constraints' => [
                    new NotBlank(
                        message: 'Le code postal est obligatoire.'
                    ),

                    // EXACTEMENT 5 chiffres → entre 10000 et 99999
                    new Range(
                        min: 10000,
                        max: 99999,
                        notInRangeMessage: 'Le code postal doit contenir exactement 5 chiffres.'
                    ),
                ],
                'attr' => [
                    'min' => 10000,
                    'max' => 99999,
                    'placeholder' => 'Code postal (5 chiffres)',
                    'inputmode' => 'numeric',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
