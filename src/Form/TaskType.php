<?php

namespace App\Form;

use App\Entity\Task;
use App\Priorities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            //choice field with set of possible priority values
            ->add('priority', ChoiceType::class,
                array('choices' => array(Priorities::A => Priorities::A,
                                         Priorities::B => Priorities::B,
                                         Priorities::C => Priorities::C,
                                         Priorities::D => Priorities::D,
                                         Priorities::E => Priorities::E,
                                         Priorities::F => Priorities::F)))
            ->add('deadline', DateType::class, [
                'widget' => 'single_text',
            ])
            //field with behavior which allows user to add multiple tags without reloading page
            ->add('tags', CollectionType::class, array(
                'entry_type' => TagType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,

            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
