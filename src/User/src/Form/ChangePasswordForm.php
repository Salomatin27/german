<?php
namespace User\Form;

use App\Form\Form;
use Doctrine\ORM\EntityManager;
use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Callback;
use Mezzio\Csrf\SessionCsrfGuard;

/**
 * This form is used when changing user's password (to collect user's old password
 * and new password) or when resetting user's password (when user forgot his password).
 */
class ChangePasswordForm extends Form
{
    // There can be two scenarios - 'change' or 'reset'.
    private $scenario;

    /**
     * @var SessionCsrfGuard
     */
    private $csrfGuard;

    /**
     * Constructor.
     * @param EntityManager $entityManager
     * @param SessionCsrfGuard $csrfGuard
     * @param string $scenario Either 'change' or 'reset'.
     */
    public function __construct(EntityManager $entityManager, SessionCsrfGuard $csrfGuard, $scenario)
    {
        // Define form name
        parent::__construct($entityManager, 'change-password-form');
        $this->csrfGuard = $csrfGuard;

        $this->scenario = $scenario;

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // If scenario is 'change', we do not ask for old password.
        if ($this->scenario == 'change') {
            // Add "old_password" field
            $this->add([
                'type'  => 'password',
                'name' => 'old_password',
                'options' => [
                    'label' => 'Старый пароль',
                ],
                'attributes'=> [
                    'class' => $this->class,
                ],
            ]);
        }

        // Add "new_password" field
        $this->add([
            'type'  => 'password',
            'name' => 'new_password',
            'options' => [
                'label' => 'Новый пароль',
            ],
            'attributes'=> [
                'class' => $this->class,
            ],
        ]);

        // Add "confirm_new_password" field
        $this->add([
            'type'  => 'password',
            'name' => 'confirm_new_password',
            'options' => [
                'label' => 'Подтвердите новый пароль',
            ],
            'attributes'=> [
                'class' => $this->class,
            ],
        ]);

        // Add the CSRF field
        $this->add([
            'type' => Hidden::class,
            'name' => 'csrf',
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        if ($this->scenario == 'change') {
            // Add input for "old_password" field
            $inputFilter->add([
                'name'     => 'old_password',
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
        }

        $inputFilter->add([
            'name' => 'csrf',
            'required' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => function ($value) {
                            return $this->csrfGuard
                                ->validateToken($value, 'user_reset');
                        },
                        'messages' => [
                            'callbackValue' => 'Formular nicht von der ursprünglichen Website überprüft',
                        ],
                    ],
                ],
            ],
        ]);

        // Add input for "new_password" field
        $inputFilter->add([
            'name'     => 'new_password',
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

        // Add input for "confirm_new_password" field
        $inputFilter->add([
            'name'     => 'confirm_new_password',
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'new_password',
                    ],
                ],
            ],
        ]);
    }
}
