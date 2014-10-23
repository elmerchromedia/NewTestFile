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

use Management\UserBundle\Controller\DefaultController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testIndexAction(Request $req)
    {
        $client = static::createClient();

        // goes to the secure page
        $crawler = $client->request('POST', '/login');

        // redirects to the login page
        $crawler = $client->followRedirect('login');

        // submits the login form
        $form = $crawler->selectButton('login')->form(array('_email' => 'admin', '_password' => 'adminpass'));
        $client->submit($form);

        // redirect to the original page (but now authenticated)
        $crawler = $client->followRedirect('dashboard');

        // check that the page is the right one
        $this->assertCount(1, $crawler->filter('table.title:contains("Username")'));

        // click on the secure link
        $link = $crawler->selectLink('dashboard')->link();
        $crawler = $client->click($link);

        // check that the page is the right one
        $this->assertCount(1, $crawler->filter('div.title:contains("Welcome")'));
    }

     public function testSignupAction(Request $request) 
    {
        $client = static::createClient();

        // goes to the secure page
        $crawler = $client->request('POST', '/signup');

        // redirects to the signup page
        $crawler = $client->followRedirect('signup');

        // submits the signup form
        $form = $crawler->selectButton('form')->form(array('_email' => 'admin', '_password' => 'adminpass', '_firstname' => 'Elmer', '_lastname' => 'Malinao', '_id' => '1'));
        $client->submit($form);

        // redirect to the original page (but now authenticated)
        $crawler = $client->followRedirect('dashboard');

        // check that the page is the right one
        $this->assertCount(1, $crawler->filter('table.title:contains("Username")'));

        // click on the secure link
        $link = $crawler->selectLink('dashboard')->link();
        $crawler = $client->click($link);

        // check that the page is the right one
        $this->assertCount(1, $crawler->filter('div.title:contains("Welcome")'));
    }

    public function testdashboardAction()
    {   
  
    }

    public function testchangePassAction() 
    {
        
    }
  

    public function testforgotPassAction()
    {
        
    }

    public function testAction() 
    {
        
    }

    public function testresetPassAction() 
    {
       
       
    }

    /**
     * Send an verification email
     */
    public function testsendEmail($data) 
    {
    
    }

    /**
     * Activate user account
     */

    public function testactivateAccountAction() 
    {
        
    }

    public function testdateDiff($date1p, $date2p) 
    {

    }
}
