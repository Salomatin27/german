<?php
declare(strict_types=1);

namespace Patient\Service;

use App\Entity\Clinic;
use App\Entity\Operation;
use App\Entity\Patient;
use App\Entity\Surgeon;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Patient\Form\PatientForm;

class PatientService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getPatientsOld($para, $page) : array
    {
        $keyword = isset($para['keyword']) ? $para['keyword'] : null;

        if ($page) {
            $page = $page*100;
        } else {
            $page = 0;
        }
        $sql='exec get_patients :keyword,:page,100';
        $query=$this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('keyword', $keyword);
        $query->bindValue('page', $page);
        $query->execute();
        $result=$query->fetchAll();
        return $result;
    }

    public function getPatients($para, $page) : array
    {
        $keyword = !empty($para['keyword']) ? $para['keyword'] . '%' : null;

        if ($page) {
            $page = $page*100;
        } else {
            $page = 0;
        }
        if ($keyword) {
            $dql = 'SELECT p FROM \App\Entity\Patient p '
                . 'WHERE p.firstName LIKE :keyword OR p.surname LIKE :keyword';
        } else {
            $dql = 'SELECT p FROM \App\Entity\Patient p ';
        }
        $query = $this->entityManager->createQuery($dql)
            ->setFirstResult($page)->setMaxResults(100);
        if ($keyword) {
            $query->setParameter('keyword', $keyword);
        }
        $query->execute();
        $result=$query->getResult();
        return $result;
    }

    public function createPatient($identity) : \StdClass
    {
        $error = null;
        $patient = null;
        try {
            $patient = new Patient();
            $patient->setFirstName('*')->setSurname('*');
            $this->entityManager->persist($patient);
            $this->entityManager->flush($patient);
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }
        return (object)['patient'=>$patient, 'error'=>$error];
    }

    public function removePatient($patient_id, $identity) : \stdClass
    {
        $this->entityManager->clear();
        $error = null;
        $name = null;
        /** @var Patient $patient */
        $patient = $this->entityManager->getRepository(Patient::class)
            ->find($patient_id);
        if (!$patient) {
            $error = 'No patient found to delete / Kein Patient zum Löschen gefunden';
        } else {
            try {
                $name = $patient->getFirstName() . ' ' . $patient->getSurname();
                $this->entityManager->remove($patient);
                $this->entityManager->flush($patient);
            } catch (\Exception $exception) {
                $error = $exception->getMessage();
            }
        }

        return (object)['name' => $name, 'error' => $error];
    }

    public function getPatient($patient_id)
    {
        /** @var Patient $item */
        $item = $this->entityManager->getRepository(Patient::class)->find($patient_id);
        return $item;
    }

    public function initPatientForm(Patient $patient, \Mezzio\Csrf\SessionCsrfGuard $guard)
    {
        $form = new PatientForm($this->entityManager, $patient, $guard);
        return $form;
    }

    public function setPatient(Patient $patient, array $data, $identity) : \stdClass
    {
        $error = null;

        // form1 -> patient data
        // form2 -> patient photo
        $data2 = [];
        foreach ($data as $key => $value) {
            if ($key != 'image') {
                $data2[$key] = $value;
            }
        }

        try {
            $hydrator = new DoctrineObject($this->entityManager);
            /** @var Patient $patient */
            $patient = $hydrator->hydrate($data2, $patient);
//
//            // operations
//            $obj_operations = $data['operation'] ?? null;
//            /** @var Operation[] $operations */
//            $operations = $patient->getOperation();
//            if ($operations) {
//                foreach ($operations as $operation) {
//                    $updated_key = null;
//                    if ($obj_operations) {
//                        foreach ($obj_operations as $key => $obj_operation) {
//                            if ($operation->getOperationId() == $obj_operation['operationId']) {
//                                $operation = $hydrator->hydrate($obj_operation, $operation);
//                                /** @var Surgeon $surgeon */
//                                $surgeon = $this->updateSurgeon($obj_operation);
//                                $clinic = $this->updateClinic($obj_operation);
//                                $operation->setSurgeon($surgeon);
//                                $operation->setClinic($clinic);
//                                $updated_key = $key;
//                                break;
//                            }
//                        }
//                    }
//                    if ($updated_key) {
//                        unset($obj_operations[$updated_key]);
//                    } else {
//                        $this->entityManager->remove($operation);
//                    }
//                }
//            }
//
            // logger
            $logger = new DebugStack();
            $this->entityManager->getConnection()->getConfiguration()->setSQLLogger($logger);

            // save
            $this->entityManager->flush();

            // dump
//            var_dump($logger);
//            exit();
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }


        return (object)['patient'=>$patient, 'error'=>$error];
    }

    public function getAllSurgeons()
    {
        return $this->entityManager->getRepository(Surgeon::class)->findAll();
    }

    public function getAllClinics()
    {
        return $this->entityManager->getRepository(Clinic::class)->findAll();
    }

    public function createOperation(Patient $patient, $identity) : \stdClass
    {
        $error = null;
        $output = null;
        try {
            $item = new Operation();
            $item->setPatient($patient);
            $this->entityManager->persist($item);
            $this->entityManager->flush($item);
            $hydrator = new DoctrineObject($this->entityManager);
            $output = $hydrator->extract($item);
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return (object)['operation'=>$output, 'error'=>$error];
    }

    public function removeOperation($id) : string
    {
        $this->entityManager->clear();
        $message = 'deleted';
        /** @var Patient $patient */
        $operation = $this->entityManager->getRepository(Operation::class)
            ->find($id);
        if (!$operation) {
            $message = 'No operation found to delete / Kein Operation zum Löschen gefunden';
        } else {
            try {
                $this->entityManager->remove($operation);
                $this->entityManager->flush($operation);
            } catch (\Exception $exception) {
                $message = $exception->getMessage();
            }
        }

        return $message;
    }

    /** Get Reference
     * @param string $item
     * @return array|null */

    public function getReference($item)
    {
        switch ($item) {
            case 'surgeon':
                $data = $this->entityManager->getRepository(Surgeon::class)->findAll();
                break;
            case 'clinic':
                $data = $this->entityManager->getRepository(Clinic::class)->findAll();
                break;
            default:
                $data = null;
        }

        return $data;
    }

    public function setSurgeon(?array $data)
    {
        $id = $data['id'];
        if ($id === '0') {
            $surgeon = new Surgeon();
        } else {
            $surgeon = $this->entityManager->getRepository(Surgeon::class)->find($id);
        }

        $surgeon->setSurgeonName($data['name'] ?? '*');

        if ($id === '0') {
            $this->entityManager->persist($surgeon);
        }
        $this->entityManager->flush($surgeon);

        return $surgeon;
    }

    public function setClinic(?array $data)
    {
        $id = $data['id'];
        if ($id === '0') {
            $clinic = new Clinic();
        } else {
            $clinic = $this->entityManager->getRepository(Clinic::class)->find($id);
        }

        $clinic->setClinicName($data['name'] ?? '*');
        $clinic->setClinicAddress($data['address'] ?? '*');

        if ($id === '0') {
            $this->entityManager->persist($clinic);
        }
        $this->entityManager->flush($clinic);

        return $clinic;
    }

    public function removeReference($id, $item)
    {
        switch ($item) {
            case 'surgeon':
                $entity = $this->entityManager->getRepository('App\Entity\Surgeon')->find($id);
                break;
            case 'clinic':
                $entity = $this->entityManager->getRepository('App\Entity\Clinic')->find($id);
                break;
            default:
                $entity = null;
        }
        if (!$entity) {
            return 'invalid reference or not found';
        }
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush($entity);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return 'deleted';
    }

    private function updateClinic($data)
    {
        /** @var Clinic $clinic */
        $clinic = null;
        $name = $data['clinicName']?? null;
        $address = $data['clinicAddress']?? null;
        $id = $data['clinicId'] ?? null;
        if (!$id && $name) {
            $clinic = new Clinic();
            $clinic->setClinicName($name);
            $clinic->setClinicAddress($address);
            try {
                $this->entityManager->persist($clinic);
            } catch (\Exception $exception) {
                $clinic = null;
            }
        }
        if ($id) {
            $clinic = $this->entityManager->getRepository(Clinic::class)
                ->find($id);
            $clinic->setClinicName($name)->setClinicAddress($address);
        }
        return $clinic;
    }

    private function updateSurgeon($data)
    {
        /** @var Surgeon $surgeon */
        $surgeon = null;
        $name = $data['surgeonName']?? null;
        $id = $data['surgeonId'] ?? null;
        if (!$id && $name) {
            $surgeon = new Surgeon();
            $surgeon->setSurgeonName($name);
            try {
                $this->entityManager->persist($surgeon);
            } catch (\Exception $exception) {
                $surgeon = null;
            }
        }
        if ($id) {
            $surgeon = $this->entityManager->getRepository(Surgeon::class)
                ->find($id);
            $surgeon->setSurgeonName($name);
        }
        return $surgeon;
    }

    public function getOperationsByPatient(Patient $patient)
    {
        $sql = 'select surgeon.surgeon_name as surgeonName,clinic.clinic_name as clinicName,'
            . 'clinic.clinic_address as clinicAddress,'
            . 'operation.operation_id as operationId,operation.surgeon_id as surgeonId,'
            . 'operation.clinic_id as clinicId, '
            . 'operation.patient_id as patientId,date_format(operation.date, \'%d-%m-%Y %H:%i\') as date, '
            . 'operation.remarks, '
            . 'operation.case_number as caseNumber, '
            . 'operation.patient_height as patientHeight, '
            . 'operation.patient_weight as patientWeight '
            . 'from operation left join surgeon on surgeon.surgeon_id=operation.surgeon_id '
            . 'left join clinic on clinic.clinic_id=operation.clinic_id '
            . 'where operation.patient_id=:id';

        $query=$this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('id', $patient->getPatientId());
        $query->execute();
        return $query->fetchAll();
    }
}
