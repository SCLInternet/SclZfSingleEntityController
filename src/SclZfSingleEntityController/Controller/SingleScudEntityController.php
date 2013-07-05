<?php

namespace SclZfSingleEntityController\Controller;

use SclZfUtilities\Mapper\GenericMapperInterface;
use SclZfSingleEntityController\Exception\NoEntityException;

/**
 * A generic controller which provides add, edit, delete & view actions for a
 * single entity type.
 *
 * @author Tom Oram
 * @todo   Add success/failure messages.
 */
class SingleScudEntityController extends SingleEntityController implements
    SingleEntityControllerInterface
{
    /**
     * Display a searchable list of entities.
     *
     * @return array
     */
    public function indexAction()
    {
        return array(
            $this->listVariable => $this->getSearchable(
                $this->getMapper(),
                'domainSI',
                'findAll'
            )
        );
    }

    /**
     * Display a form to add a new entity.
     *
     * @return array
     */
    public function addAction()
    {
        $entity = $this->getMapper()->create();

        $form = $this->getFormBuilder()->createForm($entity, 'Add');

        //$form->remove('id')->getInputFilter()->remove('id');

        if ($this->getFormBuilder()->save($entity, $form)) {
            return $this->redirectToRoute($this->redirectRoute);
        }

        return array('form' => $form);
    }

    /**
     * Display a form to edit an entity.
     *
     * @return array
     * @throws NoEntityException When no entity is loaded.
     */
    public function editAction()
    {
        $entity = $this->getEntity();

        if (null == $entity) {
            throw new NoEntityException(__METHOD__);
        }

        $form = $this->getFormBuilder()->createForm($entity, 'Edit');

        if ($this->getFormBuilder()->save($entity, $form)) {
            return $this->redirectToRoute($this->redirectRoute);
        }

        return array(
            'id'   => $entity->getId(), // @todo Enforce the existence of getId()
            'form' => $form
        );
    }

    /**
     * Display an entity.
     *
     * @return array
     * @throws NoEntityException When no entity is loaded.
     */
    public function viewAction()
    {
        $entity = $this->getEntity();

        if (null == $entity) {
            throw new NoEntityException(__METHOD__);
        }

        return array($this->viewVariable => $entity);
    }

    /**
     * Prompt the user to find if they want to delete an entity.
     *
     * @return array
     * @throws NoEntityException When no entity is loaded.
     * @todo   Add checks that delete requests are valid.
     */
    public function deleteAction()
    {
        $entity = $this->getEntity();

        if (null == $entity) {
            throw new NoEntityException(__METHOD__);
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');

            if ($del == 'Yes') {
                $this->getMapper()->delete($entity);
            }

            return $this->redirectToRoute($this->redirectRoute);
        }

        return array(
            'id'                => $entity->getId(),
            $this->viewVariable => $entity
        );
    }
}
