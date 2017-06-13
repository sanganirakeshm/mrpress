<?php

namespace Ld\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ld\UserBundle\Repository\PermissionCategoryRepository;

class PermissionFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('name', null, array('required' => true))
        		->add('code', null, array('required' => false))
        		->add('status', 'choice', array('choices'  => array(1 => 'Active',0 =>'Inactive'),'required' => true, 'empty_value' => 'Select Status'))
        		->add('category', 'entity', array(
	        		'class' => 'LdUserBundle:PermissionCategory',
	        		'property' => 'name',
	        		'label' => 'Category',        		
	        		'required' => true,
                    'multiple' => false,
                	'query_builder' => function(PermissionCategoryRepository $pc) {
                    	return $pc->getAllActivePermissionCategory();
                	}
        		));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(array(
            'data_class' => 'Ld\UserBundle\Entity\Permission',
        	'validation_groups' => array('userPermission', 'updatePermission'),
        ));
    }

    public function getName() {

        return 'ld_admin_permission';
    }
}
