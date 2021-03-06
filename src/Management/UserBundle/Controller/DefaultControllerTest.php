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

    public function testIndexAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // correct login flow
        // test we are in the login page and status code is 200
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Login Now")')->count());
      
        $form = $crawler->selectButton('submit')->form(array(
         'login[email]'  => 'elmer.malinao@chromedia.com',
         'login[password]'  => 'elmer123',
       ));
        $client->submit($form);
        $crawler = $client->getRequest('dashboard'); // "/" page
    }
    public function testSignupAction() 
    {
      $client = static::createClient();
        $crawler = $client->request('GET', '/signup');

        // correct login flow
        // test we are in the login page and status code is 200
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sign Up")')->count());
      
        $form = $crawler->selectButton('signup[save]')->form(array(
         'signup[email]'  => 'elmer123',
         'signup[password][first]'  => 'elmer123',
         'signup[password][second]'  => 'elmer123',
         'signup[firstname]'  => 'elmer123',
         'signup[lastname]'  => 'elmer123',
       ));
        $client->submit($form);
        $crawler = $client->getRequest('signup'); // "/" page  
    }
    public function testdashboardAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // correct login flow
        // test we are in the login page and status code is 200
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Login Now")')->count());
      
        $form = $crawler->selectButton('submit')->form(array(
         'login[email]'  => 'elmer.malinao@chromedia.com',
         'login[password]'  => 'elmer123',
       ));
        $client->submit($form);
        //$crawler = $client->followRedirect('dashboard'); // "/" page
        $crawler = $client->getRequest('dashboard'); // "/" page
    }

    /*public function testIndexAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // correct login flow
        // test we are in the login page and status code is 200
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Login Now")')->count());
        $this->assertEquals(200, $client->getResponse()->getStatus());

        // test submit with correct credentials
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array('Email' => 'elmer.malinao@chromedia.com', 'Password' => 'elmer123'));

        // assert that it was redirected to /dashboard
        $this->assertEquals(302, $client->getResponse()->getStatus());
        $this->assertEquals('http://symfony.localhost/app_dev.php/dashboard', $client->getResponse()->headers->get('dashboard'));
        //---- end correct login flow

         //---- logout flow validation
        // request to /login
        $crawler = $client->request('GET', '/login');
        $logoutLink = $crawler->selectLink('logout')->eq(0)->link();
        $crawler = $client->click($logoutLink); // click logout link
        
        // assert that it was redirected to /login after clicking the logout link
        $this->assertEquals(302, $client->getResponse()->getStatus());
        $this->assertTrue($this->isRedirectedToLoginPage($client));
        //---- end logout flow

        //---- login with invalid credentials
        // request to login page and test for invalid login
        $crawler = $client->request('GET', '/login');
        
        // test submit with invalid credentials
        $formValues = array('Email' => 'elmer.malinao@gmail.com', 'Password' => '8546974585245698122369852211447785222258747456374563365477456336547951'.time());
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Bad credentials")')->count());
        //---- end login with invalid credentials

        //---- login with missing required fields
        $crawler = $client->request('GET', '/login');
        
        // test submit with missing required fields
        $formValues = array();
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Bad credentials")')->count()); 
        // look for the text "Bad credentials"
        //---- end login with missing required fields -->

    }*/
}

