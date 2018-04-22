<?php
/**
 * Created by PhpStorm.
 * User: jenik
 * Date: 4/22/18
 * Time: 9:05 PM
 */

namespace App\Form;


use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('text')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }

}