<?php

namespace App\Form;

use App\Constants;
use App\Entity\Role;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //choice field with set of role types
            ->add('type', ChoiceType::class,
                array('choices' => array(Constants::LEADER =>Constants::LEADER,
                                         Constants::ADMIN => Constants::ADMIN,
                                         Constants::USER => Constants::USER)))
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}
