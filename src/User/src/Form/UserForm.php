<?php
namespace User\Form;

use App\Entity\Surgeon;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectSelect;
use App\Entity\Role;
use App\Entity\User;
use App\Form\Form;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Validator\Callback;
use Mezzio\Csrf\SessionCsrfGuard;
use User\Filter\UserPhoneFilter;
use Laminas\Filter\StringTrim;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\InputFilter;
use User\Validator\UserExistsValidator;
use Laminas\Validator\Hostname;
use Laminas\Validator\StringLength;

class UserForm extends Form
{
    /**
     * Scenario ('create' or 'update').
     * @var string
     */
    private $scenario;

    /**
     * Current user.
     * @var User
     */
    private $user = null;

    /**
     * @var SessionCsrfGuard
     */
    private $csrfGuard;

    /**
     * Constructor.
     * @param EntityManager $entityManager
     * @param User $user
     * @param SessionCsrfGuard $csrfGuard
     * @param string $scenario
     */
    public function __construct(
        EntityManager $entityManager,
        User $user,
        SessionCsrfGuard $csrfGuard,
        $scenario = 'create'
    ) {

        // Define form name
        parent::__construct($entityManager, 'user-form');
        $this->csrfGuard = $csrfGuard;

        $this->setAttribute('method', 'post');
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->user = $user;

        $this->addElements();
        $this->addInputFilter($inputFilter);
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Role
        $this->add([
            'type' => ObjectSelect::class,
            'name' => 'role',
            'options' => [
                'label' => 'Роль',
                'object_manager' => $this->entityManager,
                'target_class' => Role::class,
                'property' => 'desc',
//                'display_empty_item' => true,
//                'empty_item_label'   => 'Не учитывать программу',
                'option_attributes'  => [
                    'class' => $this->class,
                    'data-id' => function (Role $role) {
                        return $role->getRoleId();
                    },
                    'data-name' => function (Role $role) {
                        return $role->getName();
                    },
                    'data-note' => function (Role $role) {
                        return $role->getNote();
                    },
                ],
            ],
            'attributes'=> [
                'class' => 'selectpicker',
                'style' => 'box-sizing: border-box !important;',
                'data-width' => '100%',
                'data-style' => 'btn-primary',
                'onchange' => 'changeRole(this)',
                'value' => $this->user->getRole()?$this->user->getRole()->getRoleId():null,

            ],
        ]);

        // Surgeon
        $this->add([
            'type' => ObjectSelect::class,
            'name' => 'surgeon',
            'options' => [
                'label' => 'Surgeon',
                'object_manager' => $this->entityManager,
                'target_class' => Surgeon::class,
                'property' => 'surgeonName',
                'display_empty_item' => true,
                'empty_item_label'   => '...',
                'option_attributes'  => [
                    'class' => $this->class,
                    'data-id' => function (Surgeon $surgeon) {
                        return $surgeon->getSurgeonId();
                    },
                    'data-name' => function (Surgeon $surgeon) {
                        return $surgeon->getSurgeonName();
                    },
                ],
            ],
            'attributes'=> [
                'class' => 'selectpicker',
                'style' => 'box-sizing: border-box !important;',
                'data-width' => '100%',
                'data-db' => 'surgeon',
                'data-style' => 'btn-primary',
                'data-live-search' => 'true',
                'title' => '...',
                'value' => $this->user->getSurgeon() ? $this->user->getSurgeon()->getSurgeonId() : null,

            ],
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'email',
            'options' => [
                'label' => 'Эл-почта',
            ],

            'attributes'=> [
                'class' => $this->class,
                'value' => $this->user->getEmail(),
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'full_name',
            'options' => [
                'label' => 'Полное имя',
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->user->getFullName(),
            ],
        ]);

        if ($this->scenario == 'create') {
            // Add "password" field
            $this->add([
                'type'  => 'password',
                'name' => 'password',
                'options' => [
                    'label' => 'Пароль',
                ],
                'attributes'=> [
                    'class' => $this->class,
                ],
            ]);

            // Add "confirm_password" field
            $this->add([
                'type'  => 'password',
                'name' => 'confirm_password',
                'options' => [
                    'label' => 'Подтвердите пароль',
                ],
                'attributes'=> [
                    'class' => $this->class,
                ],
            ]);
        }

        // Add "status" field
        $this->add([
            'type'  => Select::class,
            'name' => 'status',
            'options' => [
                'label' => 'Статус',
                'value_options' => [
                    1 => 'aktiv',
                    2 => 'geschlossen',
                ],
            ],
            'attributes'=> [
                'onchange' => 'changeStatus(this)',
                'value' => $this->user->getStatus(),
                'class' => 'selectpicker',
                'style' => 'box-sizing: border-box !important;',
                'data-width' => '100%',
                'data-style' => 'btn-primary',
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Создать'
            ],
        ]);

        // Add the CSRF field
        $this->add([
            'type' => Hidden::class,
            'name' => 'csrf',
        ]);

        $this->add([
            'type'  => Text::class,
            'name' => 'phoneIn',
            'options' => [
                'label' => 'Телефон ATC',
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->user->getPhoneIn(),
                'id'    => 'phoneIn',
                'placeholder' => 'пример 101'
            ],
        ]);

        $this->add([
            'type'  => Text::class,
            'name' => 'phoneOut',
            'options' => [
                'label' => 'Телефон внешний',
            ],
            'attributes'=> [
                'class' => $this->class,
                'value' => $this->user->getPhoneOut(),
                'id'    => 'phoneOut',
                'placeholder' => 'пример 79585825957'
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     * @param InputFilter $inputFilter
     */
    private function addInputFilter($inputFilter)
    {
        $inputFilter->add([
            'name' => 'csrf',
            'required' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => function ($value) {
                            return $this->csrfGuard
                                ->validateToken($value, 'user_' . ($this->user->getUserId()?? '0'));
                        },
                        'messages' => [
                            'callbackValue' => 'Formular nicht von der ursprünglichen Website überprüft',
                        ],
                    ],
                ],
            ],
        ]);

        // Add input for "email" field
        $inputFilter->add([
            'name'     => 'email',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 128
                    ],
                ],
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => Hostname::ALLOW_DNS,
                        'useMxCheck'    => false,
                    ],
                ],
                [
                    'name' => UserExistsValidator::class,
                    'options' => [
                        'entityManager' => $this->entityManager,
                        'user' => $this->user
                    ],
                ],
            ],
        ]);

        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'full_name',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 512
                    ],
                ],
            ],
        ]);

        if ($this->scenario == 'create') {
            // Add input for "password" field
            $inputFilter->add([
                'name'     => 'password',
                'required' => true,
                'filters'  => [
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);

            // Add input for "confirm_password" field
            $inputFilter->add([
                'name'     => 'confirm_password',
                'required' => true,
                'filters'  => [
                ],
                'validators' => [
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ]);
        }

        // Add input for "status" field
        $inputFilter->add([
            'name'     => 'status',
            'required' => true,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name'=>'InArray', 'options'=>['haystack'=>[1, 2]]]
            ],
        ]);

        $inputFilter->add([
            'name'    => 'phoneIn',
            'required'=> false,
            'filters'  => [
                ['name' => StringTrim::class],
                ['name' => UserPhoneFilter::class],
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 20
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'    => 'phoneOut',
            'required'=> false,
            'filters'  => [
                ['name' => StringTrim::class],
                ['name' => UserPhoneFilter::class],
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 20
                    ],
                ],
            ],
        ]);
    }
}
