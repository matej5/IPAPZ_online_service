<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 19.02.19.
 * Time: 18:53
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $options['data'];

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Service'
                ]
            )
            ->add(
                'cost',
                NumberType::class,
                [
                    'label' => 'Cost (€)'
                ]
            )
            ->add(
                'duration',
                NumberType::class,
                [
                    'label' => 'Duration (min)'
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'Description'
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Image'
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'choices' => $choices,
                    'choice_label' => 'name',
                    'label' => 'Select category',
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false,
                    'empty_data' => 0
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
