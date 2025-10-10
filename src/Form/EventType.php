<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Category;
use App\Entity\Address;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder 
            ->add('image_event', FileType::class, [
                'label' => 'Image (optionnelle)',
                'required' => false,
                'mapped' => false,
            ])
            ->add('title_event', TextType::class, [
                'label' => 'Titre de l’événement'
            ])
            ->add('description_event', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('dateTime_event', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
            ])
            ->add('duration_event', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'required' => false,
            ])

            ->add('nbx_participant', IntegerType::class, [
                'label' => 'Nombre de participants',
                'required' => false,
            ])

            ->add('nbx_participant_max', IntegerType::class, [
                'label' => 'Nombre maximum de participants',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nameCategory',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
            ])
            ->add('address', EntityType::class, [
                'class' => Address::class,
                'choice_label' => function(Address $a) {
                    return $a->getAddress() . ', ' . $a->getCity() . ' (' . $a->getCp() . ')';
                },
                'label' => 'Adresse',
                'placeholder' => 'Choisir une adresse',
            ]);
            
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
