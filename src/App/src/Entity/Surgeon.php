<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Surgeon
 *
 * @ORM\Table(name="surgeon")
 * @ORM\Entity
 */
class Surgeon
{
    /**
     * @var int
     *
     * @ORM\Column(name="surgeon_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $surgeonId;

    /**
     * @var string
     *
     * @ORM\Column(name="surgeon_name", type="string", length=50, nullable=false, options={"comment"="хирург,врач/physician"})
     */
    private $surgeonName;



    /**
     * Get surgeonId.
     *
     * @return int
     */
    public function getSurgeonId()
    {
        return $this->surgeonId;
    }

    /**
     * Set surgeonName.
     *
     * @param string $surgeonName
     *
     * @return Surgeon
     */
    public function setSurgeonName($surgeonName)
    {
        $this->surgeonName = $surgeonName;

        return $this;
    }

    /**
     * Get surgeonName.
     *
     * @return string
     */
    public function getSurgeonName()
    {
        return $this->surgeonName;
    }
}
