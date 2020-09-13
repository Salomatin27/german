<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Spine
 *
 * @ORM\Table(name="spine")
 * @ORM\Entity
 */
class Spine
{
    /**
     * @var int
     *
     * @ORM\Column(name="spine_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $spineId;

    /**
     * @var string
     *
     * @ORM\Column(name="spine_name", type="string", length=50, nullable=false)
     */
    private $spineName;



    /**
     * Get spineId.
     *
     * @return int
     */
    public function getSpineId()
    {
        return $this->spineId;
    }

    /**
     * Set spineName.
     *
     * @param string $spineName
     *
     * @return Spine
     */
    public function setSpineName($spineName)
    {
        $this->spineName = $spineName;

        return $this;
    }

    /**
     * Get spineName.
     *
     * @return string
     */
    public function getSpineName()
    {
        return $this->spineName;
    }
}
