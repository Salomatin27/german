<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kind
 *
 * @ORM\Table(name="kind")
 * @ORM\Entity
 */
class Kind
{
    /**
     * @var int
     *
     * @ORM\Column(name="kind_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $kindId;

    /**
     * @var string
     *
     * @ORM\Column(name="operation_kind", type="string", length=50, nullable=false, options={"comment"="вид операции"})
     */
    private $operationKind;



    /**
     * Get kindId.
     *
     * @return int
     */
    public function getKindId()
    {
        return $this->kindId;
    }

    /**
     * Set operationKind.
     *
     * @param string $operationKind
     *
     * @return Kind
     */
    public function setOperationKind($operationKind)
    {
        $this->operationKind = $operationKind;

        return $this;
    }

    /**
     * Get operationKind.
     *
     * @return string
     */
    public function getOperationKind()
    {
        return $this->operationKind;
    }
}
