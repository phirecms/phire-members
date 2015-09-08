<?php

namespace Phire\Members\Controller;

use Phire\Members\Form\Login;
use Phire\Form;
use Phire\Model;
use Phire\Table;
use Phire\Controller\AbstractController;
use Pop\Auth;
use Pop\Application;
use Pop\Http\Request;
use Pop\Http\Response;

class IndexController extends AbstractController
{

    /**
     * Member name
     * @var string
     */
    protected $memberName = null;

    /**
     * Member URI
     * @var string
     */
    protected $memberUri = null;

    /**
     * Member role ID
     * @var int
     */
    protected $memberRoleId = null;

    /**
     * Member redirect
     * @var int
     */
    protected $memberRedirect = null;

    /**
     * Constructor for the controller
     *
     * @param  Application $application
     * @param  Request     $request
     * @param  Response    $response
     * @param  string      $memberName
     * @param  string      $memberUri
     * @param  string      $roleId
     * @param  string      $redirect
     * @return IndexController
     */
    public function __construct(
        Application $application, Request $request, Response $response, $memberName, $memberUri, $roleId, $redirect = null
    )
    {
        $this->memberName     = $memberName;
        $this->memberUri      = $memberUri;
        $this->memberRoleId   = $roleId;
        $this->memberRedirect = $redirect;

        parent::__construct($application, $request, $response);
    }

    /**
     * Index action method
     *
     * @return void
     */
    public function index()
    {
        $this->prepareView('members-public/index.phtml');
        $this->view->title = $this->memberName;
        $this->send();
    }

    /**
     * Login action method
     *
     * @return void
     */
    public function login()
    {
        $this->prepareView('members-public/login.phtml');
        $this->view->title = $this->memberName . ' : Login';

        $fields = $this->application->config()['forms']['Phire\Members\Form\Login'];
        $fields['role_id']['value'] = $this->memberRoleId;

        $this->view->form  = new Login($fields);

        if ($this->request->isPost()) {
            $auth = new Auth\Auth(
                new Auth\Adapter\Table(
                    'Phire\Table\Users',
                    Auth\Auth::ENCRYPT_BCRYPT
                )
            );

            $this->view->form->addFilter('strip_tags')
                 ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
                 ->setFieldValues($this->request->getPost(), $auth);

            if ($this->view->form->isValid()) {
                $this->sess->member = new \ArrayObject([
                    'id'       => $auth->adapter()->getUser()->id,
                    'role_id'  => $auth->adapter()->getUser()->role_id,
                    'role'     => Table\Roles::findById($auth->adapter()->getUser()->role_id)->name,
                    'username' => $auth->adapter()->getUser()->username,
                    'email'    => $auth->adapter()->getUser()->email,
                ], \ArrayObject::ARRAY_AS_PROPS);

                if (!empty($this->memberRedirect)) {
                    $path = BASE_PATH . $this->memberRedirect;
                } else if (php_sapi_name() != 'cli') {
                    $path = BASE_PATH . $this->memberUri;
                    if ($path == '') {
                        $path = '/';
                    }
                }

                $this->redirect($path);
            }
        }

        $this->send();
    }

    /**
     * Register action method
     *
     * @param  int $id
     * @return void
     */
    public function register($id)
    {
        $role = new Model\Role();

        if (($id == $this->memberRoleId) && ($role->canRegister($id, 'member-register'))) {
            $this->prepareView('members-public/register.phtml');
            $this->view->title = $this->memberName . ' : Register';

            $captcha = (isset($this->application->config()['registration_captcha']) &&
                ($this->application->config()['registration_captcha']));

            $csrf = (isset($this->application->config()['registration_csrf']) &&
                ($this->application->config()['registration_csrf']));

            $role->getById($id);

            if ($role->email_as_username) {
                $fields = $this->application->config()['forms']['Phire\Form\RegisterEmail'];
                $fields[2]['role_id']['value'] = $id;
                $this->view->form = new Form\RegisterEmail($captcha, $csrf, $fields);
            } else {
                $fields = $this->application->config()['forms']['Phire\Form\Register'];
                $fields[2]['role_id']['value'] = $id;
                if ($role->email_required) {
                    $fields[1]['email']['required'] = true;
                }
                $this->view->form = new Form\Register($captcha, $csrf, $fields);
            }

            if ($this->request->isPost()) {
                $this->view->form->addFilter('strip_tags')
                     ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
                     ->setFieldValues($this->request->getPost());

                if ($this->view->form->isValid()) {
                    $this->view->form->clearFilters()
                         ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                         ->filter();

                    $fields = $this->view->form->getFields();
                    $role->getById($id);
                    $fields['active']   = (int)!($role->approval);
                    $fields['verified'] = (int)!($role->verification);

                    $user = new Model\User();
                    $user->save($fields);

                    $this->view->id      = $user->id;
                    $this->view->success = true;
                }
            }
            $this->send();
        } else {
            $this->redirect(BASE_PATH . '/');
        }
    }

    /**
     * Profile action method
     *
     * @return void
     */
    public function profile()
    {
        $this->prepareView('members-public/profile.phtml');
        $this->view->title = $this->memberName . ' : Profile';

        $user = new Model\User();
        $user->getById($this->sess->member->id);

        $role = new Model\Role();
        $role->getById($this->sess->member->role_id);

        if ($role->email_as_username) {
            $fields = $this->application->config()['forms']['Phire\Form\ProfileEmail'];
            $fields[2]['role_id']['value'] = $this->sess->member->role_id;
            $this->view->form = new Form\ProfileEmail($fields);
        } else {
            $fields = $this->application->config()['forms']['Phire\Form\Profile'];
            $fields[2]['role_id']['value'] = $this->sess->member->role_id;
            if ($role->email_required) {
                $fields[1]['email']['required'] = true;
            }
            $this->view->form = new Form\Profile($fields);
        }

        $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
             ->setFieldValues($user->toArray());

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                 ->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();

                $fields = $this->view->form->getFields();
                $role   = new Model\Role();
                $role->getById($this->sess->member->role_id);
                $fields['active']   = $user->active;
                $fields['verified'] = $user->verified;

                $user = new Model\User();
                $user->update($fields, $this->sess);
                $this->view->id = $user->id;
                $this->sess->setRequestValue('saved', true, 1);
                $this->redirect(BASE_PATH . $this->memberUri . '/profile');
            }
        }

        $this->send();
    }

    /**
     * Verify action method
     *
     * @param  int    $id
     * @param  string $hash
     * @return void
     */
    public function verify($id, $hash)
    {
        $user = new Model\User();
        $this->prepareView('members-public/verify.phtml');
        $this->view->title  = $this->memberName . ' : Verify Your Email';
        $this->view->result = $user->verify($id, $hash);
        $this->view->id = $user->id;
        $this->send();
    }

    /**
     * Forgot action method
     *
     * @return void
     */
    public function forgot()
    {
        $this->prepareView('members-public/forgot.phtml');
        $this->view->title = $this->memberName . ' : Password Reminder';

        $this->view->form = new Form\Forgot($this->application->config()['forms']['Phire\Form\Forgot']);

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                 ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
                 ->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();

                $user = new Model\User();
                $user->forgot($this->view->form->getFields());
                unset($this->view->form);
                $this->view->id      = $user->id;
                $this->view->success = true;
            }
        }

        $this->send();
    }

    /**
     * Unsubscribe action method
     *
     * @return void
     */
    public function unsubscribe()
    {
        $this->prepareView('members-public/unsubscribe.phtml');
        $this->view->title = $this->memberName . ' : Unsubscribe';

        $this->view->form = new Form\Unsubscribe($this->application->config()['forms']['Phire\Form\Unsubscribe']);

        if ($this->request->isPost()) {
            $this->view->form->addFilter('strip_tags')
                 ->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
                 ->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();

                $user = new Model\User();
                $user->unsubscribe($this->view->form->getFields());
                $this->view->success = true;
                $this->view->id      = $user->id;
                $this->sess->kill();
                $this->redirect(BASE_PATH . $this->memberUri . '/unsubscribe?success=1');
            }
        }

        $this->send();
    }

    /**
     * Logout action method
     *
     * @return void
     */
    public function logout()
    {
        $this->sess->kill();
        $this->redirect(BASE_PATH . $this->memberUri . '/login');
    }

    /**
     * Get member name
     *
     * @return string
     */
    public function getMemberName()
    {
        return $this->memberName;
    }

    /**
     * Get member URI
     *
     * @return string
     */
    public function getMemberUri()
    {
        return $this->memberUri;
    }

    /**
     * Get member role ID
     *
     * @return int
     */
    public function getMemberRoleId()
    {
        return $this->memberRoleId;
    }

    /**
     * Prepare view
     *
     * @param  string $template
     * @return void
     */
    protected function prepareView($template)
    {
        $this->viewPath = __DIR__ . '/../../view';

        parent::prepareView($template);

        if (isset($this->sess->member)) {
            $this->view->member = $this->sess->member;
        }

        $this->view->memberName     = $this->memberName;
        $this->view->member_name    = $this->memberName;
        $this->view->memberUri      = $this->memberUri;
        $this->view->member_uri     = $this->memberUri;
        $this->view->memberPath     = $this->memberUri;
        $this->view->member_path    = $this->memberUri;
        $this->view->memberRoleId   = $this->memberRoleId;
        $this->view->member_role_id = $this->memberRoleId;
    }

}