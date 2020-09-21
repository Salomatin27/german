<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email_idx", columns={"email"})}, indexes={@ORM\Index(name="user_lng_lng_id_fk", columns={"lng_id"}), @ORM\Index(name="user_surgeon_surgeon_id_fk", columns={"surgeon_id"}), @ORM\Index(name="user_role_role_id_fk", columns={"role_id"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=512, nullable=false)
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=256, nullable=false)
     */
    private $password;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pwd_reset_token", type="string", length=32, nullable=true)
     */
    private $pwdResetToken;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="pwd_reset_token_date", type="datetime", nullable=true)
     */
    private $pwdResetTokenDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="subdiv_id", type="integer", nullable=true)
     */
    private $subdivId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone_in", type="string", length=20, nullable=true)
     */
    private $phoneIn;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone_out", type="string", length=20, nullable=true)
     */
    private $phoneOut;

    /**
     * @var \App\Entity\Lng
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Lng")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lng_id", referencedColumnName="lng_id")
     * })
     */
    private $lng;

    /**
     * @var \App\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="role_id")
     * })
     */
    private $role;

    /**
     * @var \App\Entity\Surgeon
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Surgeon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="surgeon_id", referencedColumnName="surgeon_id")
     * })
     */
    private $surgeon;



    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fullName.
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateCreated.
     *
     * @param \DateTime $dateCreated
     *
     * @return User
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set pwdResetToken.
     *
     * @param string|null $pwdResetToken
     *
     * @return User
     */
    public function setPwdResetToken($pwdResetToken = null)
    {
        $this->pwdResetToken = $pwdResetToken;

        return $this;
    }

    /**
     * Get pwdResetToken.
     *
     * @return string|null
     */
    public function getPwdResetToken()
    {
        return $this->pwdResetToken;
    }

    /**
     * Set pwdResetTokenDate.
     *
     * @param \DateTime|null $pwdResetTokenDate
     *
     * @return User
     */
    public function setPwdResetTokenDate($pwdResetTokenDate = null)
    {
        $this->pwdResetTokenDate = $pwdResetTokenDate;

        return $this;
    }

    /**
     * Get pwdResetTokenDate.
     *
     * @return \DateTime|null
     */
    public function getPwdResetTokenDate()
    {
        return $this->pwdResetTokenDate;
    }

    /**
     * Set subdivId.
     *
     * @param int|null $subdivId
     *
     * @return User
     */
    public function setSubdivId($subdivId = null)
    {
        $this->subdivId = $subdivId;

        return $this;
    }

    /**
     * Get subdivId.
     *
     * @return int|null
     */
    public function getSubdivId()
    {
        return $this->subdivId;
    }

    /**
     * Set phoneIn.
     *
     * @param string|null $phoneIn
     *
     * @return User
     */
    public function setPhoneIn($phoneIn = null)
    {
        $this->phoneIn = $phoneIn;

        return $this;
    }

    /**
     * Get phoneIn.
     *
     * @return string|null
     */
    public function getPhoneIn()
    {
        return $this->phoneIn;
    }

    /**
     * Set phoneOut.
     *
     * @param string|null $phoneOut
     *
     * @return User
     */
    public function setPhoneOut($phoneOut = null)
    {
        $this->phoneOut = $phoneOut;

        return $this;
    }

    /**
     * Get phoneOut.
     *
     * @return string|null
     */
    public function getPhoneOut()
    {
        return $this->phoneOut;
    }

    /**
     * Set lng.
     *
     * @param \App\Entity\Lng|null $lng
     *
     * @return User
     */
    public function setLng(\App\Entity\Lng $lng = null)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng.
     *
     * @return \App\Entity\Lng|null
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set role.
     *
     * @param \App\Entity\Role|null $role
     *
     * @return User
     */
    public function setRole(\App\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return \App\Entity\Role|null
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set surgeon.
     *
     * @param \App\Entity\Surgeon|null $surgeon
     *
     * @return User
     */
    public function setSurgeon(\App\Entity\Surgeon $surgeon = null)
    {
        $this->surgeon = $surgeon;

        return $this;
    }

    /**
     * Get surgeon.
     *
     * @return \App\Entity\Surgeon|null
     */
    public function getSurgeon()
    {
        return $this->surgeon;
    }
}
