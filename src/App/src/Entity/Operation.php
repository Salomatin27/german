<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Operation
 *
 * @ORM\Table(name="operation", indexes={@ORM\Index(name="operation_surgeon_surgeon_id_fk", columns={"surgeon_id"}), @ORM\Index(name="operation_patient_patient_id_fk", columns={"patient_id"}), @ORM\Index(name="operation_kind_kind_id_fk", columns={"kind_id"}), @ORM\Index(name="operation_clinic_clinic_id_fk", columns={"clinic_id"})})
 * @ORM\Entity
 */
class Operation
{
    /**
     * @ORM\OneToMany(targetEntity="\App\Entity\OperationImplant", mappedBy="operation")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="operation_id")
     */
    protected $operationImplant;
    public function __construct()
    {
        $this->operationImplant = new ArrayCollection();
    }
    public function getOperationImplant()
    {
        return $this->operationImplant;
    }
    public function addOperationImplant(Collection $operationImplant)
    {
        foreach ($operationImplant as $item) {
            $item->setOperation($this);
            $this->operationImplant->add($item);
        }
    }
    public function removeOperationImplant(Collection $operationImplant)
    {
        foreach ($operationImplant as $item) {
            $item->setOperation(null);
            $this->operationImplant->removeElement($item);
        }
    }

    /**
     * @var int
     *
     * @ORM\Column(name="operation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $operationId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true, options={"comment"="дата операции"})
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remarks", type="text", length=16777215, nullable=true, options={"comment"="замечания"})
     */
    private $remarks;

    /**
     * @var string|null
     *
     * @ORM\Column(name="case_number", type="string", length=10, nullable=true, options={"comment"="номер операции"})
     */
    private $caseNumber;

    /**
     * @var int|null
     *
     * @ORM\Column(name="patient_height", type="integer", nullable=true, options={"comment"="рост до операции"})
     */
    private $patientHeight;

    /**
     * @var int|null
     *
     * @ORM\Column(name="patient_weight", type="integer", nullable=true, options={"comment"="вес до операции"})
     */
    private $patientWeight;

    /**
     * @var \App\Entity\Clinic
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Clinic")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="clinic_id", referencedColumnName="clinic_id")
     * })
     */
    private $clinic;

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
     * @var \App\Entity\Patient
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="operation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="patient_id")
     * })
     */
    private $patient;

    /**
     * @var \App\Entity\Surgeon
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Surgeon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="surgeon_id", referencedColumnName="surgeon_id")
     * })
     */
    private $surgeon;



    /**
     * Get operationId.
     *
     * @return int
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    /**
     * Set date.
     *
     * @param \DateTime|null $date
     *
     * @return Operation
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set remarks.
     *
     * @param string|null $remarks
     *
     * @return Operation
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
     * Set caseNumber.
     *
     * @param string|null $caseNumber
     *
     * @return Operation
     */
    public function setCaseNumber($caseNumber = null)
    {
        $this->caseNumber = $caseNumber;

        return $this;
    }

    /**
     * Get caseNumber.
     *
     * @return string|null
     */
    public function getCaseNumber()
    {
        return $this->caseNumber;
    }

    /**
     * Set patientHeight.
     *
     * @param int|null $patientHeight
     *
     * @return Operation
     */
    public function setPatientHeight($patientHeight = null)
    {
        $this->patientHeight = $patientHeight;

        return $this;
    }

    /**
     * Get patientHeight.
     *
     * @return int|null
     */
    public function getPatientHeight()
    {
        return $this->patientHeight;
    }

    /**
     * Set patientWeight.
     *
     * @param int|null $patientWeight
     *
     * @return Operation
     */
    public function setPatientWeight($patientWeight = null)
    {
        $this->patientWeight = $patientWeight;

        return $this;
    }

    /**
     * Get patientWeight.
     *
     * @return int|null
     */
    public function getPatientWeight()
    {
        return $this->patientWeight;
    }

    /**
     * Set clinic.
     *
     * @param \App\Entity\Clinic|null $clinic
     *
     * @return Operation
     */
    public function setClinic(\App\Entity\Clinic $clinic = null)
    {
        $this->clinic = $clinic;

        return $this;
    }

    /**
     * Get clinic.
     *
     * @return \App\Entity\Clinic|null
     */
    public function getClinic()
    {
        return $this->clinic;
    }

    /**
     * Set kind.
     *
     * @param \App\Entity\Kind|null $kind
     *
     * @return Operation
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
     * Set patient.
     *
     * @param \App\Entity\Patient|null $patient
     *
     * @return Operation
     */
    public function setPatient(\App\Entity\Patient $patient = null)
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * Get patient.
     *
     * @return \App\Entity\Patient|null
     */
    public function getPatient()
    {
        return $this->patient;
    }

    /**
     * Set surgeon.
     *
     * @param \App\Entity\Surgeon|null $surgeon
     *
     * @return Operation
     */
    public function setSurgeon(\App\Entity\Surgeon $surgeon = null)
    {
        $this->surgeon = $surgeon;

        return $this;
    }

    /**
     * Get surgeon.
     *
     * @return \App\Entity\Surgeon|null
     */
    public function getSurgeon()
    {
        return $this->surgeon;
    }
}
