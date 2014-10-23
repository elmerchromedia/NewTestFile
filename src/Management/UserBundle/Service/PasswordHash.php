<?php 
/**
 * researched by: elmer
 * resource link: https://www.google.com.ph
 * 
 */
namespace Management\UserBundle\Service;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class PasswordHash implements PasswordEncoderInterface
{

    public function encodePassword($raw, $salt)
    {
        return hash('sha256', $salt . $raw); // Custom function for password encrypt
    }

   	public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }

}