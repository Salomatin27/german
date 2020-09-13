<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperationImplant
 *
 * @ORM\Table(name="operation_implant", indexes={@ORM\Index(name="oi_operation_operation_id_fk", columns={"operation_id"}), @ORM\Index(name="oi_implant_implant_id_fk", columns={"implant_id"})})
 * @ORM\Entity
 */
class OperationImplant
{
    /**
     * @var int
     *
     * @ORM\Column(name="operation_implant_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $operationImplantId;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=1023, nullable=false)
     */
    private $code;

    /**
     * @var \App\Entity\Implant
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Implant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="implant_id", referencedColumnName="implant_id")
     * })
     */
    private $implant;

    /**
     * @var \App\Entity\Operation
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operation_id", referencedColumnName="operation_id")
     * })
     */
    private $operation;



    /**
     * Get operationImplantId.
     *
     * @return int
     */
    public function getOperationImplantId()
    {
        return $this->operationImplantId;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return OperationImplant
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set implant.
     *
     * @param \App\Entity\Implant|null $implant
     *
     * @return OperationImplant
     */
    public function setImplant(\App\Entity\Implant $implant = null)
    {
        $this->implant = $implant;

        return $this;
    }

    /**
     * Get implant.
     *
     * @return \App\Entity\Implant|null
     */
    public function getImplant()
    {
        return $this->implant;
    }

    /**
     * Set operation.
     *
     * @param \App\Entity\Operation|null $operation
     *
     * @return OperationImplant
     */
    public function setOperation(\App\Entity\Operation $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation.
     *
     * @return \App\Entity\Operation|null
     */
    public function getOperation()
    {
        return $this->operation;
    }
}
