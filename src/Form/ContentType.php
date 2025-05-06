<?php

namespace App\Form;

use App\Entity\Content;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => 'Identifiant unique',
                'required' => true,
                'help' => 'Un identifiant unique pour ce contenu (ex: about_image, contact_info, etc.)',
                'attr' => [
                    'placeholder' => 'about_image, contact_info, team_member_1'
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => [
                    'rows' => 10,
                    'class' => 'wysiwyg-editor'
                ],
            ])
            ->add('section', TextType::class, [
                'label' => 'Section',
                'required' => false,
                'help' => 'Catégorisation du contenu (ex: A propos, Contact, Équipe)',
                'attr' => [
                    'placeholder' => 'A propos, Contact, Équipe'
                ]
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
                'help' => 'Les images haute définition sont acceptées (max 20Mo)',
                'help_attr' => [
                    'class' => 'text-info small'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Content::class,
        ]);
    }
} 