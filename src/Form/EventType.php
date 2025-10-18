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

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title_event', TextType::class, [
                'label' => 'Titre de l\'événement',
                'constraints' => [
                    new NotBlank(message: 'Le titre est obligatoire.'),
                ],
            ])
            ->add('description_event', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('dateTime_event', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'required' => true,
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'), // format HTML5 : YYYY-MM-DDTHH:MM
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => new \DateTime(), // empêche les dates passées même côté serveur
                        'message' => 'La date et l\'heure doivent être supérieures ou égales à maintenant.',
                    ]),
                ],
            ])
            ->add('duration_event', IntegerType::class, [
                'label' => 'Durée (en heures)',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(value: 0, message: 'La durée ne peut pas être négative.'),
                ],
            ])
            ->add('nbx_participant', IntegerType::class, [
                'label' => 'Nombre de participants',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(value: 1, message: 'Il doit y avoir au moins un participant.'),
                ],
            ])
            ->add('nbx_participant_max', IntegerType::class, [
                'label' => 'Nombre maximum de participants',
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(value: 1, message: 'Le nombre maximum doit être au moins 1.'),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nameCategory',
                'label' => 'Catégorie',
            ])
            ->add('image_event', FileType::class, [
                'label' => 'Image de l\'événement',
                'mapped' => false,
                'required' => false,
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
