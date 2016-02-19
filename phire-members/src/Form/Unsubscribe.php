<?php
namespace Phire\Members\Form;

use Phire\Table;
use Pop\Form\Form;
use Pop\Validator;

class Unsubscribe extends Form
{

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     * @return Unsubscribe
     */
    public function __construct(array $fields, $action = null, $method = 'post')
    {
        parent::__construct($fields, $action, $method);
        $this->setAttribute('id', ((isset(\Pop\Web\Session::getInstance()->member)) ?
            'unsubscribe-member-form' : 'unsubscribe-form'));
        $this->setIndent('    ');
    }

    /**
     * Set the field values
     *
     * @param  array $values
     * @return Unsubscribe
     */
    public function setFieldValues(array $values = null)
    {
        parent::setFieldValues($values);

        if (($_POST) && (null !== $this->email)) {
            $member = Table\Users::findBy(['email' => $this->email]);
            if (!isset($member->id)) {
                $this->getElement('email')
                     ->addValidator(new Validator\NotEqual($this->email, 'That email does not exist.'));
            } else if (null !== $member->role_id) {
                $sess         = \Pop\Web\Session::getInstance();
                $requireLogin = true;
                $role         = Table\Roles::findById($member->role_id);
                if (isset($role->id) && (null !== $role->permissions)) {
                    $permissions = unserialize($role->permissions);
                    if (isset($permissions['deny'])) {
                        foreach ($permissions['deny'] as $deny) {
                            if ($deny['resource'] == 'member-login') {
                                $requireLogin = false;
                            }
                        }
                    }
                }

                if ($requireLogin) {
                    if (!isset($sess->member) || (isset($sess->member) && ($sess->member->id != $member->id))) {
                        $memberAdmin = new \Phire\Members\Model\MembersAdmin();
                        $memberAdmin->getByRoleId($member->role_id);

                        $memberUri = (isset($memberAdmin->uri)) ? $memberAdmin->uri : APP_URI;

                        $this->getElement('email')
                             ->addValidator(new Validator\NotEqual($this->email, 'You must <a href="' .
                                 BASE_PATH . $memberUri . '/login">log in</a> to unsubscribe.'));
                    }
                }
            }
        }

        return $this;
    }

}
