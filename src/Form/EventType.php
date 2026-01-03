<?php

namespace App\Form;

use App\Entity\Event;
use App\Form\AddressType;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title_event', TextType::class, [
                'label' => 'Titre de l\'événement',
                'constraints' => [
                    new Length(
                        max: 50,
                        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
                    ),
                    new Regex(
                        pattern: '/^[0-9a-zA-ZÀ-ÿ\s\-\',\.]+$/u',
                        message: 'Le titre ne peut contenir que des lettres, chiffres, espaces et caractères spéciaux (- \' , .).'
                    ),
                ],
                'attr' => [
                    'maxlength' => 50,
                ]
            ])
            ->add('description_event', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints' => [
                    new Length(
                        max: 150,
                        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'
                    ),
                ],
                'attr' => [
                    'maxlength' => 150,
                    'rows' => 5,
                ],
            ])
            ->add('dateTime_event', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'html5' => true, // active le type datetime-local
                'input' => 'datetime', // transforme en \DateTime côté PHP
                'required' => true,
                'empty_data' => (new \DateTime())->format('Y-m-d\TH:i'),
                'constraints' => [
                    new NotBlank(message: 'La date est obligatoire.'), // validation Symfony
                    new GreaterThanOrEqual([
                        'value' => 'now',
                    ]),
                ],
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'), // min côté front
                ],
            ])

            ->add('duration_event', IntegerType::class, [
                'label' => 'Durée (en heures)',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(
                        value: 0,
                        message: 'La durée ne peut pas être négative.'
                    ),
                ],
                'attr' => [
                    'min' => 0,
                    'max' => 24,
                ],
            ])
            ->add('nbx_participant', IntegerType::class, [
                'label' => 'Nombre de participants',
                'required' => false,
                'empty_data' => 1,
                'constraints' => [
                    new GreaterThanOrEqual(
                        value: 1,
                        message: 'Il doit y avoir au moins un participant.'
                    ),
                    new Regex(
                        pattern: '/^\d+$/', 
                        message: 'Le nombre maximum doit contenir uniquement des chiffres.',
                        ),
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ])
            ->add('nbx_participant_max', IntegerType::class, [
                'label' => 'Nombre maximum de participants',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(
                        value: 1,
                        message: 'Le nombre maximum doit être au moins 1.'
                    ),
                    new Regex(
                        pattern: '/^\d+$/', // juste des chiffres
                        message: 'Le nombre maximum doit contenir uniquement des chiffres.',
                        ),
                    ],
                    'attr' => [
                        'min' => 1,
                        'max' => 100,
                        'inputmode' => 'numeric',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nameCategory',
                'label' => 'Catégorie',
                'constraints' => [
                    new NotBlank(message: 'La catégorie est obligatoire.'),
                ],
            ])
            ->add('image_event', FileType::class, [
                'label' => 'Image de l\'événement',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        maxSizeMessage: 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). Taille maximale : {{ limit }} {{ suffix }}.',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                        mimeTypesMessage: '⚠️ Format de fichier non accepté. Veuillez uploader une image (JPEG, PNG, GIF ou WEBP). Les fichiers PDF ne sont pas autorisés.'
                    ),
                ],
                'attr' => [
                    'accept' => 'image/jpeg,image/png,image/gif,image/webp'
                ]
            ])
            ->add('address', AddressType::class, [
                'label' => 'Adresse',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}