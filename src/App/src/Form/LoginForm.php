<?php

declare(strict_types = 1);

namespace App\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\StringLength;
use Laminas\Form\Form;
use Mezzio\Csrf\SessionCsrfGuard;

class LoginForm extends Form implements InputFilterProviderInterface
{
    private $guard;

    public function __construct(SessionCsrfGuard $csrfGuard)
    {
        parent::__construct('login-form');
        $this->guard = $csrfGuard;

        $this->init();
    }

    public function init()
    {
        $this->add([
            'type'  => Hidden::class,
            'name' => 'redirect_url'
        ]);

        $this->add([
            'type' => Hidden::class,
            'name' => 'csrf',
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'username',
            'options' => [
                'label' => 'Username',
            ],
        ]);

        $this->add([
            'type' => Password::class,
            'name' => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'Login',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Login',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name' => 'csrf',
                'required' => true,
                'validators' => [
                    [
                        'name' => Callback::class,
                        'options' => [
                            'callback' => function ($value) {
                                return $this->guard->validateToken($value);
                            },
                            'messages' => [
//                                'callbackValue' => 'Форма подтверждена не с оригинального сайта',
                                'callbackValue' => 'The form submitted did not originate from the expected site',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'username',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],

            [
                'name' => 'password',
                'required' => true,
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
            ],

            [
                'name'     => 'redirect_url',
                'required' => false,
                'filters'  => [
                    ['name'=> StringTrim::class]
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 2048
                        ]
                    ],
                ],
            ],

        ];
    }

}