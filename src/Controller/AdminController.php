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
namespace Phire\Members\Controller;

use Phire\Members\Model;
use Phire\Members\Form;
use Phire\Members\Table;
use Phire\Controller\AbstractController;

/**
 * Members Admin Controller class
 *
 * @category   Phire\Members
 * @package    Phire\Members
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 * @version    1.0.0
 */
class AdminController extends AbstractController
{

    /**
     * Index action method
     *
     * @return void
     */
    public function index()
    {
        $this->prepareView('members/admin/index.phtml');
        $members = new Model\MembersAdmin();

        $this->view->title   = 'Members Admin';
        $this->view->members = $members->getAll($this->request->getQuery('sort'));

        $this->send();
    }

    /**
     * Add action method
     *
     * @return void
     */
    public function add()
    {
        $this->prepareView('members/admin/add.phtml');
        $this->view->title = 'Members Admin : Add';

        $fields = $this->application->config()['forms']['Phire\Members\Form\MembersAdmin'];

        $roles = \Phire\Table\Roles::findAll();
        foreach ($roles->rows() as $role) {
            $dupe = Table\Members::findBy(['role_id' => $role->id]);
            if (!isset($dupe->id)) {
                $fields[0]['role_id']['value'][$role->id] = $role->name;
            }
        }

        $fields[1]['name']['attributes']['onkeyup'] = "phire.createSlug('/' + this.value, '#uri');";
        $this->view->form = new Form\MembersAdmin($fields);

        if ($this->request->isPost()) {
            $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
                 ->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();
                $member = new Model\MembersAdmin();
                $member->save($this->view->form->getFields());
                $this->view->id = $member->id;
                $this->sess->setRequestValue('saved', true);
                $this->redirect(BASE_PATH . APP_URI . '/members/edit/'. $member->id);
            }
        }

        $this->send();
    }

    /**
     * Edit action method
     *
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {
        $member = new Model\MembersAdmin();
        $member->getById($id);

        if (!isset($member->id)) {
            $this->redirect(BASE_PATH . APP_URI . '/members');
        }

        $this->prepareView('members/admin/edit.phtml');
        $this->view->title       = 'Members Admin';
        $this->view->member_name = $member->name;

        $fields = $this->application->config()['forms']['Phire\Members\Form\MembersAdmin'];

        $role = \Phire\Table\Roles::findById($member->role_id);
        if (isset($role->id)) {
            $fields[0]['role_id']['value'][$role->id] = $role->name;
        }

        $fields[1]['name']['attributes']['onkeyup'] = 'phire.changeTitle(this.value);';

        $this->view->form = new Form\MembersAdmin($fields);
        $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
             ->setFieldValues($member->toArray());

        if ($this->request->isPost()) {
            $this->view->form->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();
                $member = new Model\MembersAdmin();

                $member->update($this->view->form->getFields(), $this->application->module('phire-members')->config()['history']);
                $this->view->id = $member->id;
                $this->sess->setRequestValue('saved', true);
                $this->redirect(BASE_PATH . APP_URI . '/members/edit/'. $member->id);
            }
        }

        $this->send();
    }

    /**
     * Remove action method
     *
     * @return void
     */
    public function remove()
    {
        if ($this->request->isPost()) {
            $member = new Model\MembersAdmin();
            $member->remove($this->request->getPost());
        }
        $this->sess->setRequestValue('removed', true);
        $this->redirect(BASE_PATH . APP_URI . '/members');
    }

    /**
     * Prepare view
     *
     * @param  string $member
     * @return void
     */
    protected function prepareView($member)
    {
        $this->viewPath = __DIR__ . '/../../view';
        parent::prepareView($member);
    }

}
