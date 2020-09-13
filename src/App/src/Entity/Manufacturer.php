<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Manufacturer
 *
 * @ORM\Table(name="manufacturer")
 * @ORM\Entity
 */
class Manufacturer
{
    /**
     * @var int
     *
     * @ORM\Column(name="manufacturer_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $manufacturerId;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer_name", type="string", length=50, nullable=false)
     */
    private $manufacturerName;



    /**
     * Get manufacturerId.
     *
     * @return int
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * Set manufacturerName.
     *
     * @param string $manufacturerName
     *
     * @return Manufacturer
     */
    public function setManufacturerName($manufacturerName)
    {
        $this->manufacturerName = $manufacturerName;

        return $this;
    }

    /**
     * Get manufacturerName.
     *
     * @return string
     */
    public function getManufacturerName()
    {
        return $this->manufacturerName;
    }
}
