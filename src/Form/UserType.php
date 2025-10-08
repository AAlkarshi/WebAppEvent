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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar_user', FileType::class, [
                'label' => 'Photo de profil (PNG, JPG)',
                'mapped' => false, // ce champ n’est pas directement lié à l’entité
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PNG ou JPEG valide',
                    ])
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
            ])
            ->add('firstname_user', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('datebirth_user', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
            ])
            ->add('mail_user', EmailType::class, [
                'label' => 'Adresse e-mail',
            ])
            ->add('password_user', PasswordType::class, [
                'label' => 'Mot de passe',
            ])
            ->add('city_user', TextType::class, [
                'label' => 'Ville',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
