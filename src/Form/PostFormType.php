<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Title of post',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            );
        $builder
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'What\'s on your mind?',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            );
        $builder
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Upload Image'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => null
            ]
        );
    }
}
