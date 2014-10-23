<?php 

namespace Management\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Management\UserBundle\Mapper\UserMapper;
use Management\UserBundle\Form\SignUpUserForm;
use Management\UserBundle\Form\LoginForm;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;


class UserController extends Controller {

 	/**
 	 * Update user
 	 */

 	public function updateUserAction() 
 	{
 		
 		$request = Request::createFromGlobals();
 		$actionrequest = $request->request->get('editaccount');
 		
 		try {
 			$user = $this->get('security.context')->getToken()->getUser();
 			$session = new Session();
 			$actionrequest['id'] = $user->getId();
 			$update = $this->get('user.userbundle.mapper')->updateUser($actionrequest);
 			if($update) {
 				$session->getFlashBag()->add('updateuser', 0);
 			} else {
 				$session->getFlashBag()->add('updateuser', 1);
 			}

 			return $this->redirect($this->generateUrl('dashboard'));

 		} catch(Exception $e) {

 			echo $e->getMessage();
 		}
 	}

 	/**
 	 * Update User password
 	 */
 	public function updatePassAction()
 	{
 		$request 	= Request::createFromGlobals();
 		$actionrequest 	= $request->request->get('updatePassForm');
 		$session = new Session();

 		//Get current password of user
 		$searchUserCurrentPass = $this->get('user.userbundle.mapper')->searchUserById($session->get('userid')); 
 		if($searchUserCurrentPass) {
 			$currentPass = $searchUserCurrentPass->getPassword();
 		}

 		//Compare Current password to Current password inputted by user

 		if($currentPass != $actionrequest['curpassword']) {
 			$session->getFlashbag()->add('changepassword', 3);
 		} elseif($actionrequest['newpassword'] != $actionrequest['conpassword']) {
 			$session->getFlashbag()->add('changepassword', 2);
 		} else {
 			$actionrequest['id'] = $session->get('userid');
 			$savePass = $this->get('user.userbundle.mapper')->updatePass($actionrequest);
 			$session->getFlashbag()->add('changepassword', 0);
 		}

 		return $this->redirect($this->generateUrl('changepass'));

 	}

	/**
	 * Send an Reset password link
	 */
	public function sendEmailToResetPassword($data) {
		$uid 		= $data['uid'];
		$id 		= $data['id'];
		$authcode 	= $data['authcode'];

		$request 	= Request::createFromGlobals();
		$url 		= $this->generateUrl('resetpassword', array('id' => $id, 'authcode' => $authcode), true);
		
		$message = \Swift_Message::newInstance()
	        ->setSubject('Forgot Password Email Notification')
	        ->setFrom('elmer.malinao@chromedia.com')
	        ->setTo($data['email'])
	        ->setBody($this->renderView('ManagementUserBundle:Default:resetemail.html.twig', array(
																						'name' => $data['firstname'], 
																						'email' => $data['email'],
																						'url' => $url
																						)
				));

	    try{
	    	$send = $this->get('mailer')->send($message);
	    	if($send) {
	    		return true;
	    	} else {
	    		return false;
	    	}
	  	} catch (Exception $e) {
	  		echo $e->getMessage();
	  	}
	}

	

	public function saveUserConfirmationAction() {
		$request 	= Request::createFromGlobals();
		$actionrequest = $request->request->get('forgotPassForm');
		
		$isUser 			= $this->get('user.userbundle.mapper')->searchUserByEmail($actionrequest['email']);
		$actionrequest['uid'] 		= $isUser->getId();
		
		if($isUser) {
			$save = $this->get('user.userbundle.mapper')->saveUserConfirmation($isUser->getId());
			if($save) {
				$actionrequest['id'] 		= $save->getId();
				$actionrequest['uid'] 		= $isUser->getId();
				$actionrequest['authcode'] 	= $save->getAuthCode();
				$actionrequest['firstname'] = $isUser->getFirstname();

				//Send Reset Email
				$sendEmail = $this->sendEmailToResetPassword($actionrequest);
				if($sendEmail) {
					return new JsonResponse(array('error' => 0, 'msg' => 'Successfully save. Please check your email'));
				} else {
					return new JsonResponse(array('error' => 1, 'msg' => 'Errror sending email'));
				}
			} else {
				return new JsonResponse(array('error' => 1, 'msg' => 'Error'));
			}
		} else {
			return new JsonResponse(array('error' => 1, 'msg' => 'Email was not found.'));
		}
	
	}
 }