<?php 

namespace Management\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as validationType;

class EditAccountForm extends AbstractType
{
	public function buildForm(FormBuilderInterface  $builder, array $options) {
		$builder
		->add('email', 'email', array(
		    'constraints' => array(new validationType\NotNull(array('message' => 'Enter your email'))) 
		))
		
		->add('firstname', 'text', array(
 
		))
		->add('lastname', 'text',  array(
		    'constraints' => array() 
		))
		->add('update', 'submit');
	}

	public function getName() { 
        return 'editaccount';
    }
}