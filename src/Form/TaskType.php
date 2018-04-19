<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('priority')
            ->add('deadline', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('tags', CollectionType::class, array(
                'entry_type' => TagType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,

            ))
            ->add('comments')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
