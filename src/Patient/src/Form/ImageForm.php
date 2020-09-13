<?php
namespace Patient\Form;

use Laminas\Form\Element\File;
use Laminas\Form\Form;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputFilter;

class ImageForm extends Form
{
    private $id;

    public function __construct($id)
    {
        parent::__construct('image-form_' . $id);
        $this->id=$id;

        $this->setAttribute('method', 'post');
        $this->setAttribute('data-operation', $id);
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type' => File::class,
            'name' => 'file',
            'attributes' => [
                'class' => 'file',
                'accept' => 'image/*',
                'style' => 'display:none',
            ],
            'options' => [
                'label' => 'File /jpeg/png/gif.',
            ],
        ]);
    }

    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'type' => FileInput::class,
            'name' => 'file',
            'required' => true,
            'validators' => [
                ['name' => 'FileUploadFile'],
                ['name' => 'FileMimeType', 'options' => [
                    'image',
                ]],
                ['name' => 'FileSize', 'options' => ['max'=>'10MB','Message'=>'Größe begrenzt 10MB']],
                ['name' => 'FileIsImage'],
            ],

        ]);
    }
}
