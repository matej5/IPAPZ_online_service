<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 19.02.19.
 * Time: 18:53
 */

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Service',
                    'attr' => ['pattern' => '[a-zA-Z]*', 'class' => 'form-control']
                ]
            )
            ->add(
                'cost',
                NumberType::class,
                [
                    'label' => 'Cost (€)',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'duration',
                NumberType::class,
                [
                    'label' => 'Duration (min)',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'Description',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Service::class
            ]
        );
    }
}
