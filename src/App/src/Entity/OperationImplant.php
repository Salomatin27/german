<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperationImplant
 *
 * @ORM\Table(name="operation_implant", indexes={
 *     @ORM\Index(name="oi_fixation_fixation_id_fk", columns={"fixation_id"}),
 *     @ORM\Index(name="oi_implant_implant_id_fk", columns={"implant_id"}),
 *     @ORM\Index(name="oi_operation_operation_id_fk", columns={"operation_id"})})
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
     * @var string|null
     *
     * @ORM\Column(name="implant_size", type="string", length=50, nullable=true, options={"comment"="размер импланта"})
     */
    private $implantSize;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remarks", type="text", length=16777215, nullable=true)
     */
    private $remarks;

    /**
     * @var \App\Entity\Fixation
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Fixation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fixation_id", referencedColumnName="fixation_id")
     * })
     */
    private $fixation;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Operation", inversedBy="operationImplant")
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
     * Set implantSize.
     *
     * @param string|null $implantSize
     *
     * @return OperationImplant
     */
    public function setImplantSize($implantSize = null)
    {
        $this->implantSize = $implantSize;

        return $this;
    }

    /**
     * Get implantSize.
     *
     * @return string|null
     */
    public function getImplantSize()
    {
        return $this->implantSize;
    }

    /**
     * Set remarks.
     *
     * @param string|null $remarks
     *
     * @return OperationImplant
     */
    public function setRemarks($remarks = null)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks.
     *
     * @return string|null
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set fixation.
     *
     * @param \App\Entity\Fixation|null $fixation
     *
     * @return OperationImplant
     */
    public function setFixation(\App\Entity\Fixation $fixation = null)
    {
        $this->fixation = $fixation;

        return $this;
    }

    /**
     * Get fixation.
     *
     * @return \App\Entity\Fixation|null
     */
    public function getFixation()
    {
        return $this->fixation;
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
