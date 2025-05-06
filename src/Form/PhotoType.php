<?php

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un titre',
                    ]),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Portrait' => 'Portrait',
                    'Paysage' => 'Paysage',
                    'Événement' => 'Événement',
                    'Architecture' => 'Architecture',
                    'Nature' => 'Nature',
                    'Sport' => 'Sport',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une catégorie',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Décrivez votre photo (facultatif)'
                ],
            ])
            ->add('isBackground', CheckboxType::class, [
                'label' => 'Utiliser comme arrière-plan',
                'required' => false,
                'help' => 'Si activé, cette image sera utilisée comme arrière-plan du site. Une seule image peut être définie comme arrière-plan.',
                'help_attr' => [
                    'class' => 'text-info small'
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image (JPG, PNG, WebP)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WebP)',
                        'maxSizeMessage' => 'Le fichier est trop volumineux. La taille maximale autorisée est {{ limit }} ({{ limit }} = 20Mo)',
                    ])
                ],
                'help' => 'Les photos haute définition sont acceptées (max 20Mo)',
                'help_attr' => [
                    'class' => 'text-info small'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
} 