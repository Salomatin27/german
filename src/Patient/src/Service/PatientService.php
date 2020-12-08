<?php
declare(strict_types=1);

namespace Patient\Service;

use App\Entity\Clinic;
use App\Entity\Fixation;
use App\Entity\Implant;
use App\Entity\Kind;
use App\Entity\Manufacturer;
use App\Entity\Operation;
use App\Entity\OperationImplant;
use App\Entity\Patient;
use App\Entity\Surgeon;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Patient\Form\OperationImplantForm;
use Patient\Form\PatientForm;
use phpDocumentor\Reflection\Types\Object_;

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
            $dql = 'SELECT p FROM \App\Entity\Patient p join \App\Entity\Operation o '
                . 'WHERE p.firstName LIKE :keyword OR p.surname LIKE :keyword '
                . 'order by p.patientId desc';
        } else {
            $dql = 'SELECT p FROM \App\Entity\Patient p join \App\Entity\Operation o '
                . 'order by p.patientId desc';
        }
        $query = $this->entityManager->createQuery($dql)
            ->setFirstResult($page)->setMaxResults(100);
        if ($keyword) {
            $query->setParameter('keyword', $keyword);
        }
        $query->execute();
        return $query->getResult();
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
//            $logger = new DebugStack();
//            $this->entityManager->getConnection()->getConfiguration()->setSQLLogger($logger);

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
            $kind = $this->getAllKinds()[0];
            $item = new Operation();
            $item->setPatient($patient);
            $item->setKind($kind);
            $this->entityManager->persist($item);
            $this->entityManager->flush($item);
//            $hydrator = new DoctrineObject($this->entityManager);
//            $output = $hydrator->extract($item);
            $output = $item;
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return (object)['operation'=>$output, 'error'=>$error];
    }

    public function removeOperation($id) : \stdClass
    {
        $this->entityManager->clear();
        $error = null;
        /** @var Patient $patient */
        $operation = $this->entityManager->getRepository(Operation::class)
            ->find($id);
        if (!$operation) {
            $error = 'Kein Operation zum Löschen gefunden (No operation found to delete) ';
        } else {
            try {
                $this->entityManager->remove($operation);
                $this->entityManager->flush($operation);
            } catch (\Exception $exception) {
                $error = $exception->getMessage();
            }
        }

        return (object)['error' => $error];
    }

    /** Get Reference
     * @param string $item
     * @param Manufacturer|null $manufacturer
     * @param Kind|null $kind
     * @return array|null
     */

    public function getReference(string $item, Manufacturer $manufacturer = null, Kind $kind = null)
    {
//        $manufacturer = null;
//        if ($manufacturer_id) {
//            $manufacturer = $this->entityManager->getRepository(Manufacturer::class)
//                ->find($manufacturer_id);
//        }
        switch ($item) {
            case 'surgeon':
                $data = $this->getAllSurgeons();
                //$data = $this->entityManager->getRepository(Surgeon::class)->findAll();
                break;
            case 'clinic':
                $data = $this->getAllClinics();
                //$data = $this->entityManager->getRepository(Clinic::class)->findAll();
                break;
            case 'kind':
                $data = $this->getAllKinds();
                break;
            case 'manufacturer':
                $data = $this->getAllManufacturers();
                break;
            case 'implant':
                $data = $this->getImplantsByManufacturerKind($manufacturer, $kind);
                break;
            case 'fixation':
                $data = $this->getFixationsByManufacturerKind($manufacturer, $kind);
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

    public function setKind(?array $data)
    {
        $id = $data['id'];
        if ($id === '0') {
            $kind = new Kind();
        } else {
            $kind = $this->entityManager->getRepository(Kind::class)->find($id);
        }

        $kind->setOperationKind($data['name'] ?? '*');

        if ($id === '0') {
            $this->entityManager->persist($kind);
        }
        $this->entityManager->flush($kind);

        return $kind;
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

    public function removeReference($id, $item) : \stdClass
    {
        $this->entityManager->clear();
        $error = null;
        switch ($item) {
            case 'surgeon':
                $entity = $this->entityManager->getRepository('App\Entity\Surgeon')->find($id);
                break;
            case 'clinic':
                $entity = $this->entityManager->getRepository('App\Entity\Clinic')->find($id);
                break;
            case 'kind':
                $entity = $this->entityManager->getRepository('App\Entity\Kind')->find($id);
                break;
            case 'manufacturer':
                $entity = $this->entityManager->getRepository('App\Entity\Manufacturer')->find($id);
                break;
            case 'implant':
                $entity = $this->entityManager->getRepository('App\Entity\Implant')->find($id);
                break;
            case 'fixation':
                $entity = $this->entityManager->getRepository('App\Entity\Fixation')->find($id);
                break;
            default:
                $entity = null;
        }
        if (!$entity) {
            $error = 'invalid reference or not found';
        }
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush($entity);
        } catch (\Exception $exception) {
            $error =  $exception->getMessage();
        }

        return (object)['error' => $error];
    }

    public function getAllKinds()
    {
        return $this->entityManager->getRepository(Kind::class)->findAll();
    }

    public function createOperationImplant($identity, $operation_id) : \stdClass
    {
        $error = null;
        $output = null;
        /** @var Operation $operation */
        $operation = $this->entityManager->getRepository(Operation::class)
            ->find($operation_id);
        if (!$operation) {
            $error = '';
            return (object)['operation'=>null, 'error'=>$error, 'redirect'=>'/patients', 'operation_implant_id'=>null];
        }
        $patient = $operation->getPatient();
        if (!$patient) {
            $error = '';
            return (object)['operation'=>null, 'error'=>$error, 'redirect'=>'/patients', 'operation_implant_id'=>null];
        }

        $operation_implant_id = null;
        try {
            $manufacturer = $this->getAllManufacturers()[0];
            if (!$manufacturer) {
                $manufacturer = $this->createEmptyManufacturer();
            }
//            $kind = $operation->getKind();
//            $implant = $this->getImplantsByManufacturerKind($manufacturer, $kind)[0];
//            $fixation = $this->getFixationsByManufacturerKind($manufacturer, $kind)[0];
            $item = new OperationImplant();
            $item->setOperation($operation);
//            $item->setImplant($implant);
//            $item->setFixation($fixation);
            $this->entityManager->persist($item);
            $this->entityManager->flush($item);
//            $hydrator = new DoctrineObject($this->entityManager);
//            $output = $hydrator->extract($item);
            $operation_implant_id = $item->getOperationImplantId();
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return (object)[
            'operation'=>$item,
            'error'=>$error,
            'redirect'=>'/patient/' . $patient->getPatientId(),
            'operation_implant_id'=>$operation_implant_id
        ];
    }

    public function getOperationImplant($operation_implant_id)
    {
        /** @var OperationImplant $item */
        $item = $this->entityManager->getRepository(OperationImplant::class)->find($operation_implant_id);
        return $item;
    }

    public function initOperationImplantForm(OperationImplant $entity, \Mezzio\Csrf\SessionCsrfGuard $guard)
    {
        $form = new OperationImplantForm($this->entityManager, $entity, $guard);
        return $form;
    }

    public function setOperationImplant(OperationImplant $entity, array $data, $identity) : \stdClass
    {
        $error = null;
        $hydrator = new DoctrineObject($this->entityManager);
        $operation_implant = $hydrator->hydrate($data, $entity);
        try {
            $this->entityManager->flush($operation_implant);
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }
        return (object)['operation_implant'=>$operation_implant, 'error'=>$error];
    }

    public function createEmptyManufacturer() : Manufacturer
    {
        $item = new Manufacturer();
        $item->setManufacturerName('<empty>');
        $this->entityManager->persist($item);
        $this->entityManager->flush($item);
        return $item;
    }

    public function removeOperationImplant($operation_implant, $identity) : \stdClass
    {
        //$this->entityManager->clear();
        $error = null;
        try {
            $this->entityManager->remove($operation_implant);
            $this->entityManager->flush($operation_implant);
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return (object)['error' => $error];
    }

    public function setManufacturer(?array $data)
    {
        $id = $data['id'];
        if ($id === '0') {
            $manufacturer = new Manufacturer();
        } else {
            $manufacturer = $this->entityManager->getRepository(Manufacturer::class)->find($id);
        }

        $manufacturer->setManufacturerName($data['name'] ?? '*');

        if ($id === '0') {
            $this->entityManager->persist($manufacturer);
        }
        $this->entityManager->flush($manufacturer);

        return $manufacturer;
    }

    public function setImplant(?array $data)
    {
        $id = $data['id'];
        if ($id === '0') {
            $implant = new Implant();
        } else {
            $implant = $this->entityManager->getRepository(Implant::class)->find($id);
        }

        $implant->setImplantName($data['name'] ?? '*');
        $manufacturer_id = $data['manufacturer_id'] ?? 0;
        $manufacturer = $this->getManufacturerById($manufacturer_id);
        $kind_id = $data['kind_id'] ?? 0;
        $kind = $this->getKindById($kind_id);
        $implant->setManufacturer($manufacturer);
        $implant->setKind($kind);

        if ($id === '0') {
            $this->entityManager->persist($implant);
        }
        $this->entityManager->flush($implant);

        return $implant;
    }

    public function getManufacturerById($id) :Manufacturer
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = $this->entityManager->getRepository(Manufacturer::class)
            ->find($id);
        if (!$manufacturer) {
            throw new \Exception('Hersteller nicht gefunden');
        }

        return $manufacturer;
    }

    public function setFixation(?array $data)
    {
        $id = $data['id'];
        if ($id === '0') {
            $fixation = new Fixation();
        } else {
            $fixation = $this->entityManager->getRepository(Fixation::class)->find($id);
        }

        $fixation->setFixationName($data['name'] ?? '*');
        $manufacturer_id = $data['manufacturer_id'] ?? 0;
        $manufacturer = $this->getManufacturerById($manufacturer_id);
        $kind_id = $data['kind_id'] ?? 0;
        $kind = $this->getKindById($kind_id);
        $fixation->setManufacturer($manufacturer);
        $fixation->setKind($kind);

        if ($id === '0') {
            $this->entityManager->persist($fixation);
        }
        $this->entityManager->flush($fixation);

        return $fixation;
    }

    public function getKindById($id) : Kind
    {
        /** @var Kind $kind */
        $kind = $this->entityManager->getRepository(Kind::class)
            ->find($id);
        if (!$kind) {
            throw new \Exception('Typ nicht gefunden');
        }

        return $kind;
    }

    public function getOperation($id)
    {
        return $this->entityManager->getRepository(Operation::class)->find($id);
    }

    /**
     * Update patient->operation for autosave func
     * @param $id
     * @param array|null $data
     * @param $identity
     * @return \stdClass
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function patchPatient($id, ?array $data, $identity): \stdClass
    {
        $this->entityManager->clear();
        $error = null;
        try {
            $patient = $this->getPatient($id);
            $hydrator = new DoctrineObject($this->entityManager);

            // если обновление по операциям, надо обновить строку operation, hydrator fail
            if ($data['operation']) {
                $operationId = null;
                $operation_array_update = [];
                foreach ($data['operation'] as $operation_key => $operation_array) {
                    $operationId = intval($operation_key);
                    $operation_array_update = $operation_array;
                }
                //$data['operation'][$operationId]['operationId'] = $operationId;
                //$data['operation'][$operationId]['patientId'] = $id;
                $operation = $this->getOperation($operationId);
                $operation = $hydrator->hydrate($operation_array_update, $operation);
                $this->entityManager->flush($operation);
            } else {
                $patient = $hydrator->hydrate($data, $patient);
                $this->entityManager->flush($patient);
            }
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
        }

        return (object)['error' => $error];
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

    public function getOperationsByPatientOld(Patient $patient)
    {
        $sql = 'select surgeon.surgeon_name as surgeonName,clinic.clinic_name as clinicName,'
            . 'clinic.clinic_address as clinicAddress,'
            . 'operation.operation_id as operationId,operation.surgeon_id as surgeonId,'
            . 'operation.clinic_id as clinicId, '
            . 'operation.patient_id as patientId,date_format(operation.date, \'%d-%m-%Y %H:%i\') as date, '
            . 'operation.remarks, '
            . 'operation.case_number as caseNumber, '
            . 'operation.patient_height as patientHeight, '
            . 'operation.patient_weight as patientWeight, '
            . 'operation.kind_id as kindId, kind.operation_kind as operationKind '
            . 'from operation left join surgeon on surgeon.surgeon_id=operation.surgeon_id '
            . 'left join clinic on clinic.clinic_id=operation.clinic_id '
            . 'inner join kind on kind.kind_id=operation.kind_id '
            . 'where operation.patient_id=:id';

        $query=$this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('id', $patient->getPatientId());
        $query->execute();
        return $query->fetchAll();
    }

    public function getOperationsByPatient(Patient $patient) : array
    {
        $operations = $this->entityManager->getRepository(Operation::class)
            ->findBy(['patient' => $patient], ['date' => 'desc']);
        return $operations;
    }

    public function getAllManufacturers()
    {
        return $this->entityManager->getRepository(Manufacturer::class)->findAll();
    }

    public function getImplantsByManufacturerKind(?Manufacturer $manufacturer, Kind $kind)
    {
        return $this->entityManager->getRepository(Implant::class)
            ->findBy(['manufacturer'=>$manufacturer, 'kind' => $kind], ['implantName'=>'asc']);
    }

    public function getFixationsByManufacturerKind(?Manufacturer $manufacturer, Kind $kind)
    {
        return $this->entityManager->getRepository(Fixation::class)
            ->findBy(['manufacturer'=>$manufacturer, 'kind' => $kind], ['fixationName'=>'asc']);
    }

    public function printPatient($id)
    {
        $patient = $this->getPatient($id);

        // patient-1
        $file = './data/Patient_data.htm';
        $contents="";
        if ($handle = fopen($file, 'r')) {
            $contents = fread($handle, filesize($file));
            fclose($handle);
        }
        $contents = str_replace(
            '_data_surname',
            $patient->getSurname(),
            $contents
        );
        $contents = str_replace(
            '_data_first_name',
            $patient->getFirstName(),
            $contents
        );
        $contents = str_replace(
            '_data_birthday',
            $patient->getBirthday() ? $patient->getBirthday()->format('d-m-Y') : '',
            $contents
        );
        $contents = str_replace(
            '_data_street',
            $patient->getStreet(),
            $contents
        );
        $contents = str_replace(
            '_data_post_code',
            $patient->getPostCode(),
            $contents
        );
        $contents = str_replace(
            '_data_residence',
            $patient->getResidence(),
            $contents
        );
        $contents = str_replace(
            '_data_phone',
            $patient->getPhone(),
            $contents
        );
//
//        //items
//        $file = './public/doc/_item.htm';
//        $content2 = "";
//        if ($handle = fopen($file, 'r')) {
//            $content2 = fread($handle, filesize($file));
//            fclose($handle);
//        }
//        $items = $this->getItems($patient);
//        $item_contents = '';
//        $total = 0;
//        /** @var Item $item */
//        foreach ($items as $item) {
//            $tmp_contents = $content2;
//            $total += (float)$item->getCostClean();
//            $tmp_contents = str_replace('ITEM-NAME', $item->getName(), $tmp_contents);
//            $tmp_contents = str_replace('ITEM-QTY', $item->getQty(), $tmp_contents);
//            $tmp_contents = str_replace('ITEM-CLEAN', $item->getClean()->getName(), $tmp_contents);
//            $tmp_contents = str_replace('ITEM-COST', round($item->getCostClean()/$item->getQty(), 2), $tmp_contents);
//            $tmp_contents = str_replace('ITEM-TOTAL', $item->getCostClean(), $tmp_contents);
//            $tmp_contents = str_replace('ITEM-OCOST', $item->getCostObject(), $tmp_contents);
//            $tmp_contents = str_replace('ITEM-WEAR', $item->getWear(), $tmp_contents);
//            $tmp_contents = str_replace('ITEM-NOTE', $item->getNote(), $tmp_contents);
//            $item_contents .= $tmp_contents;
//        }
//
//        //bill-2
//        $file = './public/doc/_bill-2.htm';
//        $content3 = "";
//        if ($handle = fopen($file, 'r')) {
//            $content3 = fread($handle, filesize($file));
//            fclose($handle);
//        }
//        $content3 = str_replace('TOTAL', round($total, 2), $content3);
//        $content3 = str_replace('DATEIN', $patient->getDate()->format('d-m-Y'), $content3);
//
//        //other
//        $file = './public/doc/second.htm';
//        $content_other = "";
//        if ($handle = fopen($file, 'r')) {
//            $content_other = fread($handle, filesize($file));
//            fclose($handle);
//        }
//
//
//        $patient_contents = $contents.$item_contents.$content3;

        $html = '<html moznomarginboxes mozdisallowselectionprint>';
        // for new page
//        $html .= '<style type="text/css" media="print">
//                .dontprint
//                { display: none; }
//                @page
//                { size: auto; margin: 0; }
//                </style>';
        // for current page
        $html .= '<style type="text/css" media="print">
                body * 
                { visibility: hidden; }
                #print-page, #print-page *
                { visibility: visible; }
                </style>';
        $html .= '<FORM class="dontprint">
                <INPUT TYPE="button" value="Печать" onClick="window.print()">
                <hr>
                </FORM>';
        $html .= '</html>';

        // property for break '.WordSection1 { page-break-after: always; page-break-inside: avoid; }'
        // property don't break page! '#print-page { position: absolute; left: 0; top: 0; }'

        $html=$html.'<div id="print-page">' . $contents . '</div>';

        return $html;
    }
}
