<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clinic
 *
 * @ORM\Table(name="clinic")
 * @ORM\Entity
 */
class Clinic
{
    /**
     * @var int
     *
     * @ORM\Column(name="clinic_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $clinicId;

    /**
     * @var string
     *
     * @ORM\Column(name="clinic_name", type="string", length=50, nullable=false, options={"comment"="наименование"})
     */
    private $clinicName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="clinic_address", type="string", length=1023, nullable=true, options={"comment"="адрес"})
     */
    private $clinicAddress;



    /**
     * Get clinicId.
     *
     * @return int
     */
    public function getClinicId()
    {
        return $this->clinicId;
    }

    /**
     * Set clinicName.
     *
     * @param string $clinicName
     *
     * @return Clinic
     */
    public function setClinicName($clinicName)
    {
        $this->clinicName = $clinicName;

        return $this;
    }

    /**
     * Get clinicName.
     *
     * @return string
     */
    public function getClinicName()
    {
        return $this->clinicName;
    }

    /**
     * Set clinicAddress.
     *
     * @param string|null $clinicAddress
     *
     * @return Clinic
     */
    public function setClinicAddress($clinicAddress = null)
    {
        $this->clinicAddress = $clinicAddress;

        return $this;
    }

    /**
     * Get clinicAddress.
     *
     * @return string|null
     */
    public function getClinicAddress()
    {
        return $this->clinicAddress;
    }
}
