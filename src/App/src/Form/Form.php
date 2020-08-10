<?php

namespace App\Form;

use Doctrine\ORM\EntityManager;
use Laminas\Form\Element\Hidden;

class Form extends \Laminas\Form\Form
{
    /** @var  EntityManager */
    protected $entityManager;
    protected $date_format;
    protected $time_format;
    protected $class;
    protected $mobile_user_agent;

    public function __construct(
        EntityManager $entityManager,
        $name = null,
        array $options = []
    ) {
        parent::__construct($name, $options);
        $this->entityManager = $entityManager;

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('autocomplete', 'off');

        // agent for html5 date
        $agent_string=$_SERVER['HTTP_USER_AGENT'];
        $this->date_format='d-m-Y';
        $this->time_format='d-m-Y H:i';
        $this->mobile_user_agent = false;
        if (stripos($agent_string, 'iPod') || stripos($agent_string, 'iPhone') ||
            stripos($agent_string, 'iPad')  ||
            stripos($agent_string, 'webOS') || stripos($agent_string, 'android')) {
            $this->date_format='Y-m-d';
            //$this->time_format='Y-m-d H:i';
            $this->mobile_user_agent = true;
        }
        $this->class='form-control form-control-sm';

        $this->add([
            'type' => Hidden::class,
            'name' => 'isMobile',
            'attributes' => [
                'id' => 'isMobile',
                'value' => $this->mobile_user_agent,
             ],
        ]);
    }
}
