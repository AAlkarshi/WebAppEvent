<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Validator\Constraints as Assert;


class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastnameUser', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[\p{L}\s\'-]+$/u',
                        'message' => 'Le nom ne peut contenir que des lettres, espaces, apostrophes ou tirets.',
                    ]),
                     new Length(
                        max: 35,
                        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])
             ->add('firstnameUser', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[\p{L}\s\'-]+$/u',
                        'message' => 'Le prénom ne peut contenir que des lettres, espaces, apostrophes ou tirets.',
                    ]),
                     new Length(
                        max: 35,
                        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])
            ->add('cityUser', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[\p{L}\s\'-]+$/u',
                        'message' => 'La ville ne peut contenir que des lettres, espaces, apostrophes ou tirets.',
                    ]),
                     new Length(
                        max: 35,
                        maxMessage: 'Le nom de la ville ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])
            ->add('mail_user', EmailType::class, [
                'label' => 'E-mail',
                 'constraints' => [
                    new Assert\NotBlank(['message' => 'L’e-mail est obligatoire.']),
                    new Assert\Email(['message' => 'Veuillez entrer un e-mail valide.']),
                ],
            ])
           ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Nouveau mot de passe',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{14,}$/',
                        'message' => 'Le mot de passe doit comporter au moins 14 caractères, incluant une majuscule, une minuscule, un chiffre et un caractère spécial.'
                    ]),
                ],
            ])

            ->add('datebirthUser', DateType::class, [
                'widget' => 'single_text', // pour un input HTML5 type="date"
                'label' => 'Date de naissance',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de naissance est obligatoire.'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => (new \DateTime())->modify('-18 years'),
                        'message' => 'Vous devez avoir au moins 18 ans pour modifier votre profil.'
                    ]),
                ],
            ])




            ->add('avatarFile', FileType::class, [
                'label' => 'Avatar (JPG,PNG,WEBP)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png','image/gif','image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image JPEG, PNG, ou WebP valide.',
                    ]),
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


?>