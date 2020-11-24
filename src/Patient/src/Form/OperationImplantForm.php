<?php
declare(strict_types=1);

namespace Patient\Form;

use App\Entity\OperationImplant;
use App\Form\Form;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Mezzio\Csrf\SessionCsrfGuard;

class OperationImplantForm extends Form implements InputFilterProviderInterface
{

    private $entity;
    private $guard;

    public function __construct(EntityManager $entityManager, OperationImplant $entity, SessionCsrfGuard $guard)
    {
        parent::__construct($entityManager, 'operation-implant-form');
        $this->entity = $entity;
        $this->guard = $guard;

        $this->init();
    }

    public function __init()
    {
        $this->add([
            'type' => Hidden::class,
            'name' => 'csrf',
        ]);
        $this->add([
            'type'  => Hidden::class,
            'name' => 'operationImplantId',
            'options' => [
                'label' => 'patient_id',
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->entity->getOperationImplantId(),
                'id'    => 'operationImplantId',
            ],
        ]);
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
                                return $this->guard
                                    ->validateToken($value, 'operation_implant_' . $this->entity->getOperationImplantId());
                            },
                            'messages' => [
                                'callbackValue' => 'Formular nicht von der ursprünglichen Website überprüft',
                            ],
                        ],
                    ],
                ],
            ],

        ];
    }
}
