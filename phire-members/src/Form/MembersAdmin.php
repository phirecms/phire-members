<?php

namespace Phire\Members\Form;

use Pop\Form\Form;
use Pop\Validator;
use Phire\Members\Table;

class MembersAdmin extends Form
{

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     * @return MembersAdmin
     */
    public function __construct(array $fields, $action = null, $method = 'post')
    {
        parent::__construct($fields, $action, $method);
        $this->setAttribute('id', 'members-admin-form');
        $this->setIndent('    ');
    }

}