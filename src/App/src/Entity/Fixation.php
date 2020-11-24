<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fixation
 *
 * @ORM\Table(name="fixation", indexes={@ORM\Index(name="fixation_manufacturer_manufacturer_id_fk", columns={"manufacturer_id"}), @ORM\Index(name="fixation_kind_kind_id_fk", columns={"kind_id"})})
 * @ORM\Entity
 */
class Fixation
{
    /**
     * @var int
     *
     * @ORM\Column(name="fixation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $fixationId;

    /**
     * @var string
     *
     * @ORM\Column(name="fixation_name", type="string", length=50, nullable=false)
     */
    private $fixationName;

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
     * Get fixationId.
     *
     * @return int
     */
    public function getFixationId()
    {
        return $this->fixationId;
    }

    /**
     * Set fixationName.
     *
     * @param string $fixationName
     *
     * @return Fixation
     */
    public function setFixationName($fixationName)
    {
        $this->fixationName = $fixationName;

        return $this;
    }

    /**
     * Get fixationName.
     *
     * @return string
     */
    public function getFixationName()
    {
        return $this->fixationName;
    }

    /**
     * Set kind.
     *
     * @param \App\Entity\Kind|null $kind
     *
     * @return Fixation
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
     * @return Fixation
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
}
