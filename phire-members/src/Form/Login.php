<?php

namespace Phire\Members\Form;

use Pop\Auth\Auth;
use Pop\Validator;

class Login extends \Phire\Form\Login
{

    /**
     * Set the field values
     *
     * @param  array           $values
     * @param  \Pop\Auth\Auth  $auth
     * @return Login
     */
    public function setFieldValues(array $values = null, Auth $auth = null)
    {
        parent::setFieldValues($values, $auth, 'member-login');

        if (($_POST) && (null !== $this->username) && (null !== $this->password) &&
            !empty($this->role_id) && (null !== $auth)) {
            if ($this->role_id != $auth->adapter()->getUser()->role_id) {
                $this->getElement('password')
                     ->addValidator(new Validator\NotEqual($this->password, 'The login was not correct.'));
            }
        }

        return $this;
    }

}