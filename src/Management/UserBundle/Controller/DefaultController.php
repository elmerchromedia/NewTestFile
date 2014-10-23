<?php

namespace Management\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Management\UserBundle\Form\SignUpUserForm;
use Management\UserBundle\Form\LoginForm;
use Management\UserBundle\Form\EditAccountForm;
use Management\UserBundle\Form\ChangePassForm;
use Management\UserBundle\Form\ForgotPassForm;
use Management\UserBundle\Form\ResetPassForm;
use Management\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction(Request $req)
    {
        if($this->getUser()) {
             return $this->redirect($this->generateUrl('dashboard'));
        }

        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        $form = $this->createForm(new LoginForm());

        return $this->render(
            'ManagementUserBundle:Default:index.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'form' => $form->createView()
            )
        );
    }

    public function signupAction(Request $request) 
    {
        if($this->getUser()) {
            return $this->redirect('dashboard');
        } 

        $user = new User();
        $form = $this->createForm(new SignUpUserForm(), $user);
        $form->handleRequest($request);
        $actionrequest = $request->request->get('signup');
       
        
        
        if($form->isValid()) {
                //save data
                $email  = $actionrequest['email'];
                $salt   = md5(uniqid());
                $activationCode = $generatedKey = sha1(mt_rand(10000,99999).time().$email); 
                $user->setSalt($salt);
                $user->setActivationCode($activationCode);
                $password = $this->get('pw_encoder')->encodePassword($actionrequest['password']['first'], $salt);
                $user->setPassword($password);
                $isIdsave= $this->get('user.userbundle.mapper')->saveUser($user);
                $data = [
                    'email'     => $actionrequest['email'],
                    'firstname' => $actionrequest['firstname'],
                    'id'        => $isIdsave,
                    'activationCode' => $activationCode
                ];
                //Send email
                $session = $request->getSession();
                $sendEmail = $this->sendEmail($data);
                if($sendEmail) {
                    $session->getFlashBag()->add('saveuser', 0);
                } else {
                    $session->getFlashBag()->add('saveuser', 1);
                }
                return $this->redirect($this->generateUrl('signup'));
        } else {
                return $this->render('ManagementUserBundle:Default:signup.html.twig', array(
                    'form' => $form->createView(),
                    'error' => '3'
                ));

        }

    }

    public function dashboardAction()
    {   
        $request = $this->getRequest();
        $user = $this->get('security.context')->getToken()->getUser();
        $status= $user->getStatus();
        $user = $this->getUser();
        $form = $this->createForm(new EditAccountForm(), $user);
        $form->handleRequest($request);
        
        if($form->isValid()){
            $data = $request->get('editaccount');
            $updateUser = $this->get('user.userbundle.mapper')->saveUser($form->getData());
        }
        $data['form'] = $form->createView();
        return $this->render('ManagementUserBundle:Default:dashboard.html.twig', $data);
    }

    public function changePassAction() 
    {
        $request = $this->getRequest();

        $form = $this->createForm(new ChangePassForm());

        if($request->isMethod('POST')) {

        $form->handleRequest($request);

            if($form->isValid()) {

                    //Get current password of user
                    $actionrequest = $request->request->get('updatePassForm');
                    $user  = $this->get('security.context')->getToken()->getUser();
                    $searchUserCurrentPass = $this->get('user.userbundle.mapper')->searchUserById($user->getId()); 
                    if($searchUserCurrentPass) {
                        $currentPass = $searchUserCurrentPass->getPassword(); 
                    }
                    $data = [];
                    
                    //Decode password input and compare Current password to Current password inputted by user
                    $passwordInput = $this->get('pw_encoder')->encodePassword($actionrequest['password'], $searchUserCurrentPass->getSalt());

                    if($currentPass != $passwordInput) {
                        $error              = 'Invalid current password';
                        $data['password']   = $actionrequest['password'];
                    } else {
                        $user = $this->get('security.context')->getToken()->getUser();
                        $error = 'You have successfully changed your password';
                        $data['password']   = '';
                        $actionrequest['id'] = $user->getId();
                        $salt               = md5(uniqid());
                        $actionrequest['salt']      = $salt;
                        $actionrequest['password']  = $this->get('pw_encoder')->encodePassword($actionrequest['newpassword']['first'], $salt);
                        $savePass = $this->get('user.userbundle.mapper')->updateUserPass($actionrequest);
                    }   
                    $data['form'] = $form->createView();
                    $data['error'] = $error;
                    return $this->render('ManagementUserBundle:Default:changepass.html.twig', $data);
            }
        }
            $data['password'] = '';
            $data['error'] = '';
            $data['form'] = $form->createView();
            return $this->render('ManagementUserBundle:Default:changepass.html.twig', $data);

    }
  

    public function forgotPassAction()
    {
        $form = $this->createForm(new ForgotPassForm());
        $data['form'] = $form->createView();
        $data['error'] = 4;
        return $this->render('ManagementUserBundle:Default:forgotpass.html.twig', $data);
    }

    public function testAction() 
    {
        $request = Request::createFromGlobals();
        $currentRoute = $request->attributes->get('_route');
        echo $currentRoute; exit;
        die(' sample test only for elmer');
      
    }

    public function resetPassAction() 
    {
        $form = $this->createForm(new ResetPassForm());
        $request    = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();
        $method = $request->getMethod();
        $data = [];
        $data['form'] = $form->createView();
        $id = $request->query->get('id');
        $authcode= $request->query->get('authcode');
        $checkResetId = $this->get('user.userbundle.mapper')->searchConUser($id, $authcode); 
        $dateNow = date('m/d/Y');
        $dateSend = date('m/d/Y', strtotime($checkResetId->getDateSend()));
        $dateDiff = $this->dateDiff($dateNow, $dateSend);
        
        //Check reset link status
        if($checkResetId) {
            if($checkResetId->getConfirmed() == 1 || $dateDiff > 1 ) {
                $data['error'] = 5;
                $session = new Session();
                $session->getFlashBag()->add('resetlinkexpired', 1);
                return $this->redirect($this->generateUrl('login'));
            } 
        }

        if($method == 'POST') {
            $actionrequest = $request->request->get('resetPassForm');
        
            $pw =  $actionrequest['newpassword'];
            $conpw = $actionrequest['conpassword'];
            $searchConUser= $this->get('user.userbundle.mapper')->searchConUser($id, $authcode); 
 
            if($searchConUser) {
                //Update confirmation
                $searchConUser->setConfirmed(1);
                $update = $em->flush();
                //Update password
                if($pw != $conpw) {
                    $data['error'] = 2;
                    return $this->render('ManagementUserBundle:Default:resetpass.html.twig', $data);
                }
                $dataPass['id']         = $searchConUser->getUserId();
                $salt = md5(uniqid());
                $dataPass['salt'] = $salt;
                $dataPass['password']   = $this->get('pw_encoder')->encodePassword($pw, $salt);
                $updatePassword= $this->get('user.userbundle.mapper')->updateUserPass($dataPass); 
                if($updatePassword) {
                    $data['error'] = 0;
                } else  {
                    $data['error'] = 3;
                }
                return $this->render('ManagementUserBundle:Default:resetpass.html.twig', $data);
                
            } else {
                $data['error'] = 1;
                return $this->render('ManagementUserBundle:Default:resetpass.html.twig', $data);
            }

        } else {
          
            $data['error'] = 4;
            $data['id'] = $id;
            $data['authcode'] = $authcode;
            return $this->render('ManagementUserBundle:Default:resetpass.html.twig', $data);
        }
       
    }

    /**
     * Send an verification email
     */
    public function sendEmail($data) 
    {
        $request    = $this->getRequest();
        $url =   $this->generateUrl('activate', array('id' => $data['id'], 'activationCode' => $data['activationCode']), true);
        $message = \Swift_Message::newInstance()
            ->setSubject('Signup Verification Email')
            ->setFrom('elmer.malinao@chromedia.com')
            ->setTo($data['email'])
            ->setBody($this->renderView('ManagementUserBundle:Default:email.html.twig', array(
                                                                                        'name' => $data['firstname'], 
                                                                                        'email' => $data['email'],
                                                                                        'url'   => $url
                                                                                        )));
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

    /**
     * Activate user account
     */

    public function activateAccountAction() 
    {
        $request    = Request::createFromGlobals();
        $form = $this->createForm(new LoginForm());
        $id = $request->query->get('id');
        $activation = $request->query->get('activate');
        
        $activate = $this->get('user.userbundle.mapper')->activateAccount($id, $activation);
        $session = new Session();
        if($activate) {
            $session->getFlashBag()->add('activated', 1);
        } else {
            $session->getFlashBag()->add('notactivated', 0);
        }
        return $this->redirect('login');
    }

    public function dateDiff($date1p, $date2p) {
        $date1 = new \DateTime($date1p);
        $date2 = new \DateTime($date2p);
        return $date1->diff($date2)->format("%d");
    }
}
