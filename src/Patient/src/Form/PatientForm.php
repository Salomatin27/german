<?php
declare(strict_types=1);

namespace Patient\Form;

use App\Entity\Patient;
use App\Form\Form;
use App\Helper\LngLabel;
use Doctrine\ORM\EntityManager;
use Laminas\Filter\StringTrim;
use Laminas\Form\Element\DateTime;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Image;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\StringLength;
use Mezzio\Csrf\SessionCsrfGuard;

class PatientForm extends Form implements InputFilterProviderInterface
{
    /**
     * Current patient.
     * @var Patient
     */
    private $patient = null;
    /**
     * @var SessionCsrfGuard
     */
    private $csrfGuard;

    public function __construct(EntityManager $entityManager, Patient $patient, SessionCsrfGuard $csrfGuard)
    {
        parent::__construct($entityManager, 'patient-form');
        $this->csrfGuard = $csrfGuard;
        $this->patient = $patient;

        $this->init();
    }

    public function init()
    {
        $this->add([
            'type' => Hidden::class,
            'name' => 'csrf',
//            'options' => [
//                'csrf_options' => [
//                    'timeout' => 3600
//                ]
//            ],
        ]);
        $this->add([
            'type'  => Hidden::class,
            'name' => 'patientId',
            'options' => [
                'label' => 'patient_id',
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->patient->getPatientId(),
                'id'    => 'patientId',
            ],
        ]);
        $this->add([
            'type'  => Text::class,
            'name' => 'firstName',
            'options' => [
                'label' => $this->label('Vorname', 'first name'),
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->patient->getFirstName(),
                'id' => 'firstName',
            ],
        ]);
        $this->add([
            'type'  => Text::class,
            'name' => 'surname',
            'options' => [
                'label' => $this->label('Name', 'surname'),
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->patient->getSurname(),
                'id' => 'surname',
            ],
        ]);
        $this->add([
            'type'  => DateTime::class,
            'name' => 'birthday',
            'options' => [
                'label' => $this->label('Geburtsdatum', 'date of birth'),
                'format' => 'd-m-Y',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => $this->class. ' datepicker',
                'value' => $this->patient->getBirthday(),
                'id' => 'birthday',
            ],
        ]);
        $this->add([
            'type'  => Text::class,
            'name' => 'street',
            'options' => [
                'label' => $this->label('Straße', 'street'),
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->patient->getStreet(),
                'id' => 'street',
            ],
        ]);
        $this->add([
            'type'  => Text::class,
            'name' => 'postCode',
            'options' => [
                'label' => $this->label('PLZ', 'post code'),
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->patient->getPostCode(),
                'id' => 'postCode',
            ],
        ]);
        $this->add([
            'type'  => Text::class,
            'name' => 'residence',
            'options' => [
                'label' => $this->label('Ort', 'place of residence'),
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->patient->getResidence(),
                'id' => 'residence',
            ],
        ]);
        $this->add([
            'type'  => Text::class,
            'name' => 'phone',
            'options' => [
                'label' => $this->label('Telefon', 'phone number'),
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->patient->getPhone(),
                'id' => 'phone',
            ],
        ]);
        $this->add([
            'type'  => Image::class,
            'name' => 'image',
            'options' => [
                'label' => $this->label('Foto', 'photo of patient'),
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes'=> [
                'class' => 'img-thumbnail patient-photo',
                'src' => $this->getImage(),
                'id' => 'image',
            ],
        ]);


//        $this->add([
//            'type'  => Hidden::class,
//            'name' => 'surgeon',
//            'options' => [
//                'label' => 'surgeon',
//            ],
//            'attributes'=> [
//                'class' => $this->class,
//                'value' => $this->document->getCustomer() ? $this->document->getCustomer()->getCustomerid() : null,
//                'id'    => 'surgeon',
//            ],
//        ]);
//        $this->add([
//            'type'  => Text::class,
//            'name' => 'surgeon_name',
//            'options' => [
//                'label' => 'Der Chirurg',
//            ],
//            'attributes'=> [
//                'class' => $this->class,
//                'value' => $this->document->getCustomer()?$this->document->getCustomer()->getName():null,
//                'onkeyup' => 'autocompleteCustomer(this)',
//                'id' => 'surgeon_name',
//                'placeholder' => 'поиск застрахованного',
//            ],
//        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name' => 'patientId',
                'required' => false,
            ],
            [
                'name' => 'csrf',
                'required' => true,
                'validators' => [
                    [
                        'name' => Callback::class,
                        'options' => [
                            'callback' => function ($value) {
                                return $this->csrfGuard
                                    ->validateToken($value, 'patient_' . $this->patient->getPatientId());
                            },
                            'messages' => [
                                'callbackValue' => 'Форма подтверждена не с оригинального сайта',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'firstName',
                'required' => true,
                'filters'  => [
                    [
                        'name' => StringTrim::class
                    ],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 50
                        ],
                    ],
                ],

            ],
            [
                'name' => 'surname',
                'required' => true,
                'filters'  => [
                    [
                        'name' => StringTrim::class
                    ],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 50
                        ],
                    ],
                ],

            ],
            [
                'name' => 'birthday',
                'required' => false,
            ],
            [
                'name' => 'street',
                'required' => false,
                'filters'  => [
                    [
                        'name' => StringTrim::class
                    ],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 50
                        ],
                    ],
                ],
            ],
            [
                'name' => 'postCode',
                'required' => false,
                'filters'  => [
                    [
                        'name' => StringTrim::class
                    ],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 10
                        ],
                    ],
                ],
            ],
            [
                'name' => 'residence',
                'required' => false,
                'filters'  => [
                    [
                        'name' => StringTrim::class
                    ],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 1023
                        ],
                    ],
                ],
            ],
            [
                'name' => 'phone',
                'required' => false,
                'filters'  => [
                    [
                        'name' => StringTrim::class
                    ],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 20
                        ],
                    ],
                ],
            ],
            [
                'name' => 'image',
                'required' => false,
            ],
        ];
    }

    private function getImage()
    {
        $image = $this->patient->getImage();
        if ($image) {
            $output = 'data:image;base64,' . base64_encode(stream_get_contents($image));
        } else {
            $output = '/img/first.png';
        }
        return $output;
    }
}
