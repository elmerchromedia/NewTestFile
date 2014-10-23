<?php 
namespace Management\UserBundle\Mapper;

use Management\UserBundle\Entity\ConfirmationUsers;
use Doctrine\ORM\EntityManager;
use Management\UserBundle\Services\PasswordHash;


class ConfirmationUsersMapper{

	protected $em;
	protected $pwHash;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}
	/**
	 * Save User to database
	 * @param array of user info
	 */
	
	public function saveUser($id) {
		$user = new ConfirmationUsers();
		$user->setUserId($id);
		$this->em->persist($user);
		$this->em->flush();

		$save = $user->getId();
		if(!$save) {
			throw new exception('Unable to create new user confirmation');
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
		$user->setLastname($data['lastname']);
		$user->setFirstname($data['firstname']);
		$user->setStatus(0);
		$this->em->flush();

		return true;
	}

	/**
	 * Search user bu username and password for login functionalites from database
	 * @param String Usernamen and Password
	 */

	public function searchUserBy($email) {
		 $user = $this->em
        ->getRepository('ManagementUserBundle:UserConfirmation')
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
		$user->setPassword($data['password']);
		
		$this->em->flush();

		return true;
	}

	/**
	 * Activate account
	 * @param Int id
	 */
	public function activateAccount($id) {
		$user = $this->em->getRepository('ManagementUserBundle:User')->find($id);

		if(!$user) {
			throw $this->createNotFoundException(
            	'No user found for id '.$data['id']
        	);
		} else {
			if($user->getStatus() == 1) {
				throw $this->createNotFoundException(
            	'User is already activated'
        		);
		   }
		}
		$user->setStatus(1);
		$this->em->flush();

		return true;
	}

}