<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 19.02.19.
 * Time: 18:53
 */

namespace App\Form;

use App\Entity\Office;
use App\Entity\Worker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class OffWorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $offices = $options['offices'];
        $choices = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 4,
            'Thursday' => 8,
            'Friday' => 16,
            'Saturday' => 32,
            'Sunday' => 64
        ];
        $hours = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];

        $builder
            ->add(
                'office',
                EntityType::class,
                [
                    'class' => Office::class,
                    'choices' => $offices,
                    'choice_label' => 'address',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'workTime',
                NumberType::class,
                [
                    'label' => 'Work time',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'workDays',
                ChoiceType::class,
                [
                    'choices' => $choices,
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false,
                    'empty_data' => 0
                ]
            )
            ->add(
                'startTime',
                ChoiceType::class,
                [
                    'choices' => $hours,
                    'label' => 'Start of work',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            );
        $builder->get('workDays')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($intToDays) {
                        $array = [];
                        if ($intToDays & 1) {
                            $array['Monday'] = 1;
                        }

                        if ($intToDays & 2) {
                            $array['Tuesday'] = 2;
                        }

                        if ($intToDays & 4) {
                            $array['Wednesday'] = 4;
                        }

                        if ($intToDays & 8) {
                            $array['Thursday'] = 8;
                        }

                        if ($intToDays & 16) {
                            $array['Friday'] = 16;
                        }

                        if ($intToDays & 32) {
                            $array['Saturday'] = 32;
                        }

                        if ($intToDays & 64) {
                            $array['Sunday'] = 64;
                        }

                        return $array;
                    },
                    function ($workDaysAsInt) {
                        $i = 0;
                        foreach ($workDaysAsInt as $day) {
                            $i += $day;
                        }

                        return $i;
                    }
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Worker::class,
                'offices' => ''
            ]
        );
    }
}
