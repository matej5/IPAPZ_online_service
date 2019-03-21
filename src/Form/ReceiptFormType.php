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
use App\Entity\Worker;
use function Sodium\add;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiptFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'office',
                EntityType::class,
                [
                    'class' => Office::class,
                    'placeholder' => 'Select office',
                    'choices' => $options['offices'],
                    'mapped' => false
                ]
            );

        $builder->get('office')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $form->getParent()->add(
                    'worker',
                    EntityType::class,
                    [
                        'class' => Worker::class,
                        'placeholder' => 'Select worker',
                        'choices' => $form->getData()->getWorker()
                    ]
                );
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
                'offices' => ''
            ]
        );
    }
}
