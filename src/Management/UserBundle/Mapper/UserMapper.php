<?php 
namespace Management\UserBundle\Mapper;

use Management\UserBundle\Entity\User;
use Management\UserBundle\Entity\StatusofUser;
use Doctrine\ORM\EntityManager;
use Management\UserBundle\Services\PasswordHash;


class UserMapper{

	protected $em;
	protected $pwHash;

	public function __construct(EntityManager $em) {
		$this->em = $em;
		//$this->pwHash = $pwHash;
	}
	/**
	 * Save User to database
	 * @param array of user info
	 */
	
	public function saveUser($user) {
	
		$user->setStatus(0);
		$user->setRoles('ROLE_USER');
		//$user->setIsActive(0);
		$this->em->persist($user);
		$this->em->flush();

		$save = $user->getId();
		if(!$save) {
			throw new exception('Unable to create new user');
		} else {
			return $save;
		}

	}

	/**
	 * Update user to database
	 * @param array of user info
	 */

	public function updateUser($data) {
		$user = $this->em->getRepository('ManagementUserBundle:User')->find($data['id']);

		if(!$user) {
			throw $this->createNotFoundException(
            	'No user found for id '.$data['id']
        	);
		}
		//$user->setPassword($data['password']);
		$user->setLastname($data['lastname']);
		$user->setFirstname($data['firstname']);
		$this->em->flush();

		return true;
	}

	/**
	 * Search user by username and password for login functionalites from database
	 * @param String Usernamen and Password
	 */

	public function searchUserBy($email, $password) {
		 $user = $this->em
        ->getRepository('ManagementUserBundle:User')
        ->findOneBy(array(
        	'email' => $email,
        	'password' => $password));
  
		return $user;
	}

	/**
	 * Search user bu username and password for login functionalites from database
	 * @param String Usernamen and Password
	 */

	public function searchUserByEmail($email) {

		 $user = $this->em
        ->getRepository('ManagementUserBundle:User')
        ->findOneBy(array(
        	'email' => $email));
  
		return $user;
	}

	/**
	 * Search user by id
	 * @param Int id
	 */

	public function searchUserById($id) {

		$ufind = $this->em->getRepository('ManagementUserBundle:User')->find($id);
		if($ufind) {
			return $ufind;
		} else {
			return false;
		}
	} 

	/**
	 * Update User Password
	 * @param Array of data user info
	 */

	public function updateUserPass($data) {

		$user = $this->em->getRepository('ManagementUserBundle:User')->find($data['id']);

		if(!$user) {
			throw $this->createNotFoundException(
            	'No user found for id '.$data['id']
        	);
		}
		$user->setSalt($data['salt']);
		$user->setPassword($data['password']);
		
		$this->em->flush();

		return true;
	}

	/**
	 * Activate account
	 * @param Int id
	 */
	public function activateAccount($id, $activationCode) {

		$user = $this->em->getRepository('ManagementUserBundle:User')
						 ->findOneBy(array(
						 	'id' 				=> $id,
						 	'activationCode'	=> $activationCode
						 ));

		if(!$user) {
			return false;
		} 
		//current stat
		$status = $user->getStatus();
		
		if($status == 1) {
			return false;
		}
		//Save stat
		$user->setStatus(1);
		$this->em->flush();

		return true;
	}

	/**
	 * Save user Forgot password detail
	 * @param Int id
	 */
	public function saveConfirmationUsers($id) {
		$userCon = new ConfirmationUsers();
		$userCon->setUserId($id);
		$userCon->setConfirmed(0);
		$userCon->setDateSend(date('Y-m-d H:i:s'));
		$userCon->setAuthCode(sha1(uniqid().time()));
		$this->em->persist($userCon);
		$this->em->flush();
		$save = $userCon;
		if(!$save) {
			throw new exception('Unable to create new user confirmation detail');
		} else {
			return $save;
		}
	}

	public function searchConUser($id, $authcode) {
		 $userCon = $this->em
        ->getRepository('ManagementUserBundle:ConfirmationUsers')
        ->findOneBy(array(
        	'id' => $id,
        	'authCode' => $authcode));
  
		return $userCon;
	}

	public function updateUserCon($id) {

		$user = $this->em->getRepository('ManagementUserBundle:User')->find($id);

		if(!$user) {
			throw $this->createNotFoundException(
            	'No user found for id '.$data['id']
        	);
		}
		$user->setConfirmed(1);
		
		$this->em->flush();

		return true;
	}
}