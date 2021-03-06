<?php
/**
 * Phire Members Module
 *
 * @link       https://github.com/phirecms/phire-members
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Phire\Members\Form;

use Phire\Table;
use Pop\Auth\Auth;
use Pop\Form\Form;
use Pop\Validator;

/**
 * Member Login Form class
 *
 * @category   Phire\Members
 * @package    Phire\Members
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 * @version    1.0.0
 */
class Login extends Form
{

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     * @return Login
     */
    public function __construct(array $fields, $action = null, $method = 'post')
    {
        parent::__construct($fields, $action, $method);
        $this->setAttribute('id', 'member-login-form');
        $this->setIndent('    ');
    }

    /**
     * Set the field values
     *
     * @param  array           $values
     * @param  \Pop\Auth\Auth  $auth
     * @return Login
     */
    public function setFieldValues(array $values = null, Auth $auth = null)
    {
        parent::setFieldValues($values);

        if (($_POST) && (null !== $this->username) && (null !== $this->password) && (null !== $auth) && !empty($this->role_id)) {
            $auth->authenticate(
                html_entity_decode($this->username, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($this->password, ENT_QUOTES, 'UTF-8')
            );

            if (!($auth->isValid())) {
                $this->getElement('password')
                     ->addValidator(new Validator\NotEqual($this->password, 'The login was not correct.'));
            } else if (!$auth->adapter()->getUser()->verified) {
                $this->getElement('password')
                     ->addValidator(new Validator\NotEqual($this->password, 'That user is not verified.'));
            } else if (!$auth->adapter()->getUser()->active) {
                $this->getElement('password')
                     ->addValidator(new Validator\NotEqual($this->password, 'That user is blocked.'));
            } else if ($this->role_id != $auth->adapter()->getUser()->role_id) {
                $this->getElement('password')
                     ->addValidator(new Validator\NotEqual($this->password, 'The login was not correct.'));
            } else {
                $role = Table\Roles::findById($auth->adapter()->getUser()->role_id);
                if (isset($role->id) && (null !== $role->permissions)) {
                    $permissions = unserialize($role->permissions);
                    if (isset($permissions['deny'])) {
                        foreach ($permissions['deny'] as $deny) {
                            if ($deny['resource'] == 'member-login') {
                                $this->getElement('password')
                                     ->addValidator(new Validator\NotEqual(
                                        $this->password, 'That user is not allowed to login.'
                                     ));
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

}
