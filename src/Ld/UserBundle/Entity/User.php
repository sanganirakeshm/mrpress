<?php

namespace Ld\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Ld\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
	public function __construct() {
		parent::__construct();
		$this->roles = array('ROLE_USER');		
	}
	
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $lastname;

    /**
     * @ORM\Column(name="is_email_verified", type="boolean", nullable=true)
     */
    protected $isEmailVerified = false;
    
    /**
     * @ORM\Column(name="email_verification_date", type="datetime", nullable=true)     
     */
    protected $emailVerificationDate;
    
    /**
     * @ORM\Column(name="is_email_optout", type="boolean", nullable=true)
     */
    protected $isEmailOptout = false;
    
    /**
     * @ORM\Column(name="is_loggedin", type="boolean", nullable=false, options={"default":false})
     */
    protected $isloggedin = false;
    
    /**
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
     */
    protected $isDeleted = false;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="ld_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;
    
    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;
    
    public function getName() {
    	$name = '';
    	if ($this->firstname) {
    		$name .= $this->firstname;
    		if ($this->lastname) {
    			$name .= ' ';
    		}
    	} if ($this->lastname) {
    		$name .= $this->lastname;
    	} if ($name == '') {
    		return $this->username;
    	} return $name;
    }
    
    /*
     * function to get user role (single)
    */    
    public function getSingleRole() {
    	return $this->roles[0];
    }
    
    public function getGroupObject() {
    	 
    	// return first group object, there will be always single group.
    	return $this->groups[0];
    }
    
    public function getGroup() {
    	// return first group name, there will be always single group.
    	if(count($this->groups))
    		return $this->groups[0]->getName();
    	else
    		return null;
    }
    
    public function getGroupCode() {
    	// return first group name, there will be always single group.
    	if(count($this->groups))
    		return $this->groups[0]->getCode();
    	else
    		return null;
    }
    
    public function getGroupId() {
    	 
    	// return first group name, there will be always single group.
    	return $this->groups[0]->getId();
    }

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
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set isEmailVerified
     *
     * @param boolean $isEmailVerified
     * @return User
     */
    public function setIsEmailVerified($isEmailVerified)
    {
        $this->isEmailVerified = $isEmailVerified;

        return $this;
    }

    /**
     * Get isEmailVerified
     *
     * @return boolean 
     */
    public function getIsEmailVerified()
    {
        return $this->isEmailVerified;
    }

    /**
     * Set emailVerificationDate
     *
     * @param \DateTime $emailVerificationDate
     * @return User
     */
    public function setEmailVerificationDate($emailVerificationDate)
    {
        $this->emailVerificationDate = $emailVerificationDate;

        return $this;
    }

    /**
     * Get emailVerificationDate
     *
     * @return \DateTime 
     */
    public function getEmailVerificationDate()
    {
        return $this->emailVerificationDate;
    }

    /**
     * Set isEmailOptout
     *
     * @param boolean $isEmailOptout
     * @return User
     */
    public function setIsEmailOptout($isEmailOptout)
    {
        $this->isEmailOptout = $isEmailOptout;

        return $this;
    }

    /**
     * Get isEmailOptout
     *
     * @return boolean 
     */
    public function getIsEmailOptout()
    {
        return $this->isEmailOptout;
    }

    /**
     * Set isloggedin
     *
     * @param boolean $isloggedin
     * @return User
     */
    public function setIsloggedin($isloggedin)
    {
        $this->isloggedin = $isloggedin;

        return $this;
    }

    /**
     * Get isloggedin
     *
     * @return boolean 
     */
    public function getIsloggedin()
    {
        return $this->isloggedin;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return User
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean 
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
