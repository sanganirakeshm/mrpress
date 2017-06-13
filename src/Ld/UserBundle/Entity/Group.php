<?php

namespace Ld\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity
 * @ORM\Table(name="user_group")
 * @ORM\Entity(repositoryClass="Ld\UserBundle\Repository\GroupRepository")
 */
class Group extends BaseGroup {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	protected $code;
	
	/**
	 * @ORM\Column(type="array", nullable=false)
	 */
	protected $roles;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="status", type="boolean", nullable=false)
	 */
	protected $status = false;
	
	/**
	 * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
	 */
	protected $isDeleted = false;
	
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
	
	/**
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
	 *
	 */
	protected $users;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Permission")
	 * @ORM\JoinTable(name="group_permissions",
	 *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
	 * )
	 */
	protected $permissions;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		//         parent::__construct();
		$this->roles 		= array('ROLE_ADMIN');
		$this->users 		= new ArrayCollection();
		$this->permissions 	= new ArrayCollection();
	}
	
	
	public function getGroupPermissionArr() {
		$permissionArr = array();
	
		foreach($this->permissions as $permission) {
			$permissionArr[$permission->getCode()] = $permission->getCode();
		}
	
		return $permissionArr;
	}
		    
    /**
     * Set code
     *
     * @param string $code
     * @return Group
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add users
     *
     * @param \Ld\UserBundle\Entity\User $users
     * @return Group
     */
    public function addUser(\Ld\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Ld\UserBundle\Entity\User $users
     */
    public function removeUser(\Ld\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add permissions
     *
     * @param \Ld\UserBundle\Entity\Permission $permissions
     * @return Group
     */
    public function addPermission(\Ld\UserBundle\Entity\Permission $permissions)
    {
        $this->permissions[] = $permissions;

        return $this;
    }

    /**
     * Remove permissions
     *
     * @param \Ld\UserBundle\Entity\Permission $permissions
     */
    public function removePermission(\Ld\UserBundle\Entity\Permission $permissions)
    {
        $this->permissions->removeElement($permissions);
    }

    /**
     * Get permissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Group
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Group
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
     * @return Group
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
     * @return Group
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
