<?php

namespace Phire\Members\Model;

use Phire\Members\Table;
use Phire\Model\AbstractModel;

class MembersAdmin extends AbstractModel
{

    /**
     * Get all member admins
     *
     * @param  string $sort
     * @return array
     */
    public function getAll($sort = null)
    {
        $order = (null !== $sort) ? $this->getSortOrder($sort) : 'id ASC';
        return Table\Members::findAll(['order' => $order])->rows();
    }

    /**
     * Get member admin by ID
     *
     * @param  int $id
     * @return void
     */
    public function getById($id)
    {
        $member = Table\Members::findById($id);
        if (isset($member->id)) {
            $this->data = array_merge($this->data, $member->getColumns());
        }
    }

    /**
     * Save new member admin
     *
     * @param  array $fields
     * @return void
     */
    public function save(array $fields)
    {
        $member = new Table\Members([
            'role_id'  => (int)$fields['role_id'],
            'name'     => $fields['name'],
            'uri'      => $fields['uri'],
            'redirect' => (!empty($fields['redirect']) ? $fields['redirect'] : null)
        ]);
        $member->save();

        $this->data = array_merge($this->data, $member->getColumns());
    }

    /**
     * Update an existing member admin
     *
     * @param  array $fields
     * @param  int   $historyLimit
     * @return void
     */
    public function update(array $fields, $historyLimit)
    {
        $member = Table\Members::findById((int)$fields['id']);
        if (isset($member->id)) {
            $member->role_id  = (int)$fields['role_id'];
            $member->name     = $fields['name'];
            $member->uri      = $fields['uri'];
            $member->redirect = (!empty($fields['redirect']) ? $fields['redirect'] : null);
            $member->save();

            $this->data = array_merge($this->data, $member->getColumns());
        }
    }

    /**
     * Remove a member admin
     *
     * @param  array $fields
     * @return void
     */
    public function remove(array $fields)
    {
        if (isset($fields['rm_member_admins'])) {
            foreach ($fields['rm_member_admins'] as $id) {
                $member = Table\Members::findById((int)$id);
                if (isset($member->id)) {
                    $member->delete();
                }
            }
        }
    }

    /**
     * Determine if list of member admins has pages
     *
     * @param  int $limit
     * @return boolean
     */
    public function hasPages($limit)
    {
        return (Table\Members::findAll()->count() > $limit);
    }

    /**
     * Get count of member admins
     *
     * @return int
     */
    public function getCount()
    {
        return Table\Members::findAll()->count();
    }

}
