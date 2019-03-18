<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 19.02.19.
 * Time: 18:53
 */

namespace App\Form;

use App\Entity\Worker;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BossFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function ($user){
                return $user->getFirstname();
                }
            ])
            ->add('firmName', TextType::class, [
                'label' => 'Add firm name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Worker::class
        ]);
    }
}