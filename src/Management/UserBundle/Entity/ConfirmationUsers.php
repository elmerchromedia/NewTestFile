<?php

namespace Management\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfirmationUsers
 */
class ConfirmationUsers
{
     /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $confirmed;
    
    private $dateSend;

    private $authCode;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return StatusofUser
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set confirmed
     *
     * @param integer $confirmed
     * @return ConfirmationUsers
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return integer 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set dateSend
     *
     * @param string $dateSend
     * @return ConfirmationUsers
     */
    public function setDateSend($dateSend)
    {
        $this->dateSend = $dateSend;

        return $this;
    }

    /**
     * Get dateSend
     *
     * @return string 
     */
    public function getDateSend()
    {
        return $this->dateSend;
    }

    /**
     * Set authCode
     *
     * @param string $authCode
     * @return ConfirmationUsers
     */
    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;

        return $this;
    }

    /**
     * Get authCode
     *
     * @return string 
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }
}