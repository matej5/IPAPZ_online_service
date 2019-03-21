<?php


namespace App\Form;

use App\Entity\Office;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'state',
                TextType::class,
                [
                    'label' => 'State'
                ]
            );
        $builder
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'City'
                ]
            );
        $builder
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'Address'
                ]
            );
        $builder
            ->add(
                'phoneNumber',
                TextType::class,
                [
                    'label' => 'Phone'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Office::class
            ]
        );
    }
}
