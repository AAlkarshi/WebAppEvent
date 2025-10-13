<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Category;
use App\Entity\Address;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;



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
    'input' => 'datetime', // 👈 obligatoire
    'data' => new \DateTime('now', new \DateTimeZone('Europe/Paris')),
    'attr' => [
        'min' => (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('Y-m-d\TH:i'),
    ],
    'constraints' => [
        new GreaterThanOrEqual([
            'value' => new \DateTime('now', new \DateTimeZone('Europe/Paris')),
            'message' => 'La date et l’heure doivent être postérieures à l’instant présent.',
        ]),
    ],
])






            ->add('duration_event', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'required' => false,
            ])

          /*  ->add('nbx_participant', IntegerType::class, [
                'label' => 'Nombre de participants',
                'required' => false,
            ])
*/
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
             // Tout ce qui est ici sera rajouté dans une table address en BDD
            ->add('address', AddressType::class, [
                'label' => 'Nouvelle adresse',
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
