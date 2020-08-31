<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'label' => 'Ajouter une image',
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control-file'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'image est requise',
                    ]),
                    new File([
                        'maxSize' => "5M",
                        'maxSizeMessage' => 'Le fichier ne peut pas dépasser {{ limit }}',
                        'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                        'mimeTypesMessage' => "Le fichier doit être aux formats {{ types }}"
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
