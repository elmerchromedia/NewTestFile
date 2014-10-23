<?php 

namespace Management\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SignUpUserForm extends AbstractType
{
	public function buildForm(FormBuilderInterface  $builder, array $options) {
		$builder
		->add('email', 'email',  array(
			'attr' => array('class'=>'input-block-level')
			))
		->add('password', 'repeated', array(
			'attr' => array('class'=>'input-block-level'),
			'type' => 'password', 
			'options' => array('attr' => array('class' => 'input-block-level')),
		    'required' => true,
		    'first_options'  => array('label' => 'Password'),
		    'second_options' => array('label' => 'Repeat Password'),
		    'invalid_message' => 'Password did not match'
			))
		->add('firstname', 'text',array(
			'attr' => array('class'=>'input-block-level'),
			))
		->add('lastname', 'text', array(
			'attr' => array('class'=>'input-block-level'),
			))
		->add('save', 'submit', array(
			'label' => 'Add User',
			'attr' => array('class'=> 'btn btn-large btn-primary')
			));
	}

	public function getName() { 
        return 'signup';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'Management\UserBundle\Entity\User',
	    ));
	}
}