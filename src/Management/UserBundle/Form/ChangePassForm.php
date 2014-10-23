<?php 

namespace Management\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ChangePassForm extends AbstractType
{
	public function buildForm(FormBuilderInterface  $builder, array $options) {
		$builder
		->add('password', 'password', array(
			'attr' => array(
				'class' => 'input-block-level'
			),
			'constraints' => array(new NotBlank(array('message' => 'Please enter your password')) ,
								  new Length(array('min' => '6', 'minMessage' => 'Please enter your password at least 8 characters')))
		))
		->add('newpassword', 'repeated', array(
			'attr' => array('class'=>'input-block-level'),
			'type' => 'password', 
			'options' => array('attr' => array('class' => 'password-field input-block-level')),
		    'required' => true,
		    'first_options'  => array('label' => 'New Password'),
		    'second_options' => array('label' => 'Repeat Password'),
		    'invalid_message' => 'Your new password did not match.'
			))
		->add('updatepassword', 'submit', array(
			'label' => 'Update Password',
			'attr' => array('class'=> 'btn btn-large btn-primary')
		));
	}

	public function getName() { 
        return 'updatePassForm';
    }
}