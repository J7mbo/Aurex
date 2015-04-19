<?php

namespace Aurex\Application\Model\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="unique_email", columns={"email"})})
 * @ORM\Entity(repositoryClass="Aurex\Application\Model\Repository\UserRepository")
 *
 * @package SourceTest\Model\Entity
 */
class User implements AdvancedUserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=false)
     */
    private $username = '';

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password = '';

    /**
     * @var Role[]
     *
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     */
    private $roles;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = false;

    /**
     * @constructor
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @param array  $roles
     * @param bool   $enabled
     * @param bool   $userNonExpired
     * @param bool   $credentialsNonExpired
     * @param bool   $userNonLocked
     */
    public function __construct(
        $username,
        $email,
        $password,
        $roles,
        $enabled               = true,
        $userNonExpired        = true,
        $credentialsNonExpired = true,
        $userNonLocked         = true
    )
    {
        if (empty($email) || empty($password))
        {
            throw new \InvalidArgumentException('The email or password cannot be empty');
        }

        $this->username              = $username;
        $this->email                 = $email;
        $this->password              = $password;
        $this->enabled               = $enabled;
        $this->accountNonExpired     = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked      = $userNonLocked;
        $this->isActive              = $enabled;
        $this->roles                 = new ArrayCollection;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function changeEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function changeUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function changePassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        if (!$this->roles->contains($role))
        {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeRole(Role $role)
    {
        $this->roles->remove($role);

        return $this;
    }

    /**
     * @return Role[]
     *
     * @note Every user must *at least* have ROLE_USER as a role
     */
    public function getRoles()
    {
        return array_unique(array_merge($this->roles->toArray(), ['ROLE_USER']));
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials() { }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }
}
