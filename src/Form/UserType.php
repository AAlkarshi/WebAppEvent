<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\GenderUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;



class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar_user', FileType::class, [
                'label' => 'Photo de profil (PNG, JPG)',
                'mapped' => false,  
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png'],
                        mimeTypesMessage: 'Veuillez télécharger un fichier PNG ou JPEG valide'
                    )
                ],
            ])
            ->add('gender_user', ChoiceType::class, [
                'choices' => [
                    'Homme' => GenderUser::Homme,
                    'Femme' => GenderUser::Femme,
                ],
                'label' => 'Genre',
                'expanded' => true, // boutons radio
            ])
            ->add('lastname_user', TextType::class, [
                'label' => 'Nom',
                  'attr' => [
                        'maxlength' => 25, 
                    ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Le nom est obligatoire.'
                    ),
                    new Assert\Length(
                        min: 2,
                        max: 25,
                        minMessage: 'Le nom doit comporter au moins {{ limit }} caractères.',
                        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ),
                    new Assert\Regex(
                        pattern: '/^[a-zA-ZÀ-ÿ\s\'-]+$/u',
                        message: 'Le nom ne peut contenir que des lettres, espaces, apostrophes et tirets.'
                    ),
                ],
            ])
           ->add('firstname_user', TextType::class, [
                    'label' => 'Prénom',
                    'attr' => [
                        'maxlength' => 25, 
                    ],
                    'constraints' => [
                        new Assert\NotBlank(
                            message: 'Le prénom est obligatoire.'
                        ),
                        new Assert\Length(
                            min: 2,
                            max: 25,
                            minMessage: 'Le prénom doit comporter au moins {{ limit }} caractères.',
                            maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                        ),
                        new Assert\Regex(
                            pattern: '/^[a-zA-ZÀ-ÿ\s\'-]+$/u',
                            message: 'Le prénom ne peut contenir que des lettres, espaces, apostrophes et tirets.'
                        ),
                    ],
            ])
            ->add('datebirth_user', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
                 'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner votre date de naissance.'
                    ),
                    new Assert\LessThanOrEqual(
                        value: '-18 years',
                        message: 'Vous devez avoir au moins 18 ans pour vous inscrire.'
                    ),
                ],
            ])
            ->add('mail_user', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                        'maxlength' => 40, 
                    ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'L\'adresse e-mail est obligatoire.'
                    ),
                    new Assert\Email(
                        message: 'Veuillez entrer une adresse e-mail valide.'
                    ),
                    new Assert\Length(
                        max: 40,
                        maxMessage: 'L\'adresse e-mail ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])
            ->add('password_user', PasswordType::class, [
                    'label' => 'Mot de passe',
                    'mapped' => true,
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(
                            message: 'Veuillez entrer un mot de passe.'
                        ),
                        new Assert\Regex(
                            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{14,}$/',
                            message: 'Le mot de passe doit comporter au moins 14 caractères, incluant une majuscule, une minuscule, un chiffre et un caractère spécial.'
                        ),
                    ],
            ])
            ->add('city_user', TextType::class, [
                    'label' => 'Ville',
                    'attr' => [
                        'maxlength' => 35, 
                    ],
                    'constraints' => [
                        new Assert\NotBlank(
                            message: 'La ville est obligatoire.'
                        ),
                        new Assert\Length(
                            min: 2,
                            max: 35,
                            minMessage: 'Le nom de la ville doit comporter au moins {{ limit }} caractères.',
                            maxMessage: 'Le nom de la ville ne peut pas dépasser {{ limit }} caractères.'
                        ),
                        new Assert\Regex(
                            pattern: '/^[a-zA-ZÀ-ÿ\s\'-]+$/u',
                            message: 'La ville ne peut contenir que des lettres, espaces, apostrophes et tirets.'
                        ),
                    ],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
