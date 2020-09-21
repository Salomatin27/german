<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Implant
 *
 * @ORM\Table(name="implant", indexes={@ORM\Index(name="implant_kind_kind_id_fk", columns={"kind_id"}), @ORM\Index(name="implant_spine_spine_id_fk", columns={"spine_id"}), @ORM\Index(name="implant_manufacturer_manufacturer_id_fk", columns={"manufacturer_id"})})
 * @ORM\Entity
 */
class Implant
{
    /**
     * @var int
     *
     * @ORM\Column(name="implant_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $implantId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="implant_system", type="string", length=1023, nullable=true)
     */
    private $implantSystem;

    /**
     * @var string
     *
     * @ORM\Column(name="implant_name", type="string", length=1023, nullable=false)
     */
    private $implantName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="implant_type", type="string", length=1023, nullable=true)
     */
    private $implantType;

    /**
     * @var \App\Entity\Kind
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Kind")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kind_id", referencedColumnName="kind_id")
     * })
     */
    private $kind;

    /**
     * @var \App\Entity\Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Manufacturer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="manufacturer_id")
     * })
     */
    private $manufacturer;

    /**
     * @var \App\Entity\Spine
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Spine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="spine_id", referencedColumnName="spine_id")
     * })
     */
    private $spine;



    /**
     * Get implantId.
     *
     * @return int
     */
    public function getImplantId()
    {
        return $this->implantId;
    }

    /**
     * Set implantSystem.
     *
     * @param string|null $implantSystem
     *
     * @return Implant
     */
    public function setImplantSystem($implantSystem = null)
    {
        $this->implantSystem = $implantSystem;

        return $this;
    }

    /**
     * Get implantSystem.
     *
     * @return string|null
     */
    public function getImplantSystem()
    {
        return $this->implantSystem;
    }

    /**
     * Set implantName.
     *
     * @param string $implantName
     *
     * @return Implant
     */
    public function setImplantName($implantName)
    {
        $this->implantName = $implantName;

        return $this;
    }

    /**
     * Get implantName.
     *
     * @return string
     */
    public function getImplantName()
    {
        return $this->implantName;
    }

    /**
     * Set implantType.
     *
     * @param string|null $implantType
     *
     * @return Implant
     */
    public function setImplantType($implantType = null)
    {
        $this->implantType = $implantType;

        return $this;
    }

    /**
     * Get implantType.
     *
     * @return string|null
     */
    public function getImplantType()
    {
        return $this->implantType;
    }

    /**
     * Set kind.
     *
     * @param \App\Entity\Kind|null $kind
     *
     * @return Implant
     */
    public function setKind(\App\Entity\Kind $kind = null)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Get kind.
     *
     * @return \App\Entity\Kind|null
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Set manufacturer.
     *
     * @param \App\Entity\Manufacturer|null $manufacturer
     *
     * @return Implant
     */
    public function setManufacturer(\App\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer.
     *
     * @return \App\Entity\Manufacturer|null
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set spine.
     *
     * @param \App\Entity\Spine|null $spine
     *
     * @return Implant
     */
    public function setSpine(\App\Entity\Spine $spine = null)
    {
        $this->spine = $spine;

        return $this;
    }

    /**
     * Get spine.
     *
     * @return \App\Entity\Spine|null
     */
    public function getSpine()
    {
        return $this->spine;
    }
}
