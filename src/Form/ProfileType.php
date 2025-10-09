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



use Symfony\Component\Validator\Constraints as Assert;


class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastnameUser', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
             ->add('firstnameUser', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('cityUser', TextType::class, [
                'label' => 'Ville',
                'required' => false,
            ])
            ->add('mailUser', EmailType::class, [
                'label' => 'E-mail',
            ])
           ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Nouveau mot de passe',
                'constraints' => [
                  /*  new Assert\NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.'
                    ]), */
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{14,}$/',
                        'message' => 'Le mot de passe doit comporter au moins 14 caractères, incluant une majuscule, une minuscule, un chiffre et un caractère spécial.'
                    ]),
                ],
            ])

            ->add('avatarFile', FileType::class, [
                'label' => 'Avatar (JPG ou PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image JPEG ou PNG valide.',
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