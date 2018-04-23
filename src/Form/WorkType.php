<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Work;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('description')
//            ->add('startDate', DateTimeType::class, [
//                'widget' => 'single_text',
//            ])
//            ->add('endDate', DateTimeType::class, [
//                'widget' => 'single_text',
//            ])
//            ->add('task')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => $options['userRepository']->findProjectTeamMembers($options['teamId'])
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Work::class,
            'teamId' => null,
            'userRepository' => null,
        ]);
    }
}
