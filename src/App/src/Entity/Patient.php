<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Patient
 *
 * @ORM\Table(name="patient")
 * @ORM\Entity
 */
class Patient
{
    /**
     * @ORM\OneToMany(targetEntity="\App\Entity\Operation", mappedBy="patient")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="patient_id")
     */
    protected $operation;
    public function __construct()
    {
        $this->operation = new ArrayCollection();
    }
    public function getOperation()
    {
        return $this->operation;
    }
    public function addOperation(Collection $operation)
    {
        foreach ($operation as $item) {
            $item->setPatient($this);
            $this->operation->add($item);
        }
        //$this->operation[] = $operation;
    }
    public function removeOperation(Collection $operation)
    {
        foreach ($operation as $item) {
            $item->setPatient(null);
            $this->operation->removeElement($item);
        }
        //$this->operation->removeElement($operation);
    }


    /**
     * @var int
     *
     * @ORM\Column(name="patient_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $patientId;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=50, nullable=false)
     */
    private $surname;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string|null
     *
     * @ORM\Column(name="street", type="string", length=50, nullable=true)
     */
    private $street;

    /**
     * @var string|null
     *
     * @ORM\Column(name="post_code", type="string", length=10, nullable=true, options={"comment"="zip/индекс"})
     */
    private $postCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="residence", type="string", length=1023, nullable=true, options={"comment"="город проживания"})
     */
    private $residence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true, options={"comment"="телефон"})
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", length=16777215, nullable=false, options={"comment"="изображение"})
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name", type="string", length=50, nullable=false)
     */
    private $imageName;

    /**
     * @var string
     *
     * @ORM\Column(name="image_size", type="string", length=50, nullable=false)
     */
    private $imageSize;

    /**
     * @var string
     *
     * @ORM\Column(name="image_type", type="string", length=50, nullable=false)
     */
    private $imageType;



    /**
     * Get patientId.
     *
     * @return int
     */
    public function getPatientId()
    {
        return $this->patientId;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Patient
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set surname.
     *
     * @param string $surname
     *
     * @return Patient
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname.
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set birthday.
     *
     * @param \DateTime|null $birthday
     *
     * @return Patient
     */
    public function setBirthday($birthday = null)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday.
     *
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set street.
     *
     * @param string|null $street
     *
     * @return Patient
     */
    public function setStreet($street = null)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street.
     *
     * @return string|null
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set postCode.
     *
     * @param string|null $postCode
     *
     * @return Patient
     */
    public function setPostCode($postCode = null)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get postCode.
     *
     * @return string|null
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set residence.
     *
     * @param string|null $residence
     *
     * @return Patient
     */
    public function setResidence($residence = null)
    {
        $this->residence = $residence;

        return $this;
    }

    /**
     * Get residence.
     *
     * @return string|null
     */
    public function getResidence()
    {
        return $this->residence;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return Patient
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Patient
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set imageName.
     *
     * @param string $imageName
     *
     * @return Patient
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName.
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set imageSize.
     *
     * @param string $imageSize
     *
     * @return Patient
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * Get imageSize.
     *
     * @return string
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     * Set imageType.
     *
     * @param string $imageType
     *
     * @return Patient
     */
    public function setImageType($imageType)
    {
        $this->imageType = $imageType;

        return $this;
    }

    /**
     * Get imageType.
     *
     * @return string
     */
    public function getImageType()
    {
        return $this->imageType;
    }
}
