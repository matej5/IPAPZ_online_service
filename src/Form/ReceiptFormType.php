<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 19.02.19.
 * Time: 18:53
 */

namespace App\Form;

use App\Entity\Office;
use App\Entity\Receipt;
use App\Entity\User;
use App\Entity\Worker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiptFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('office', EntityType::class, [
                'class' => Office::class
            ]);
        $builder
            ->add('buyer', EntityType::class, [
                'class' => User::class
            ]);
        $builder
            ->add('worker', EntityType::class, [
                'class' => Worker::class
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Receipt::class
        ]);
    }
}