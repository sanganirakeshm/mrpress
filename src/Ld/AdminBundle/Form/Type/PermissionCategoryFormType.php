<?php

namespace Ld\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PermissionCategoryFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('name', null, array('required' => true))
        		->add('status', 'choice', array('choices'  => array(1 => 'Active',0 =>'Inactive'),'required' => true, 'empty_value' => 'Select Status'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(array(
            'data_class' => 'Ld\UserBundle\Entity\PermissionCategory',
        	'validation_groups' => array('addPermissionCategory', 'updatePermissionCategory'),
        ));
    }

    public function getName() {

        return 'ld_admin_permission_category';
    }
}
