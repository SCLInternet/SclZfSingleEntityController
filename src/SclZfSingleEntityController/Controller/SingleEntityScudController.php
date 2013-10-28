<?php

namespace SclZfSingleEntityController\Controller;

use SclZfGenericMapper\MapperInterface;

/**
 * A generic controller which provides add, edit, delete & view actions for a
 * single entity type.
 *
 * @author Tom Oram
 * @todo   Add success/failure messages.
 */
class SingleEntityScudController extends SingleEntityController implements
    SingleEntityControllerInterface
{
    /**
     * The name of the variable to be used in the list (index) action.
     *
     * @var string
     */
    protected $listVariable;

    /**
     * The name of the variable to be used in the view action.
     *
     * @var string
     */
    protected $viewVariable;

    /**
     * The route to redirect to after adding, editing and deleting.
     *
     * @var string
     */
    protected $redirectRoute;

    /**
     * The name of the list search container.
     *
     * @var string
     */
    protected $searchContainerName;

    /**
     * Set the mapper for the controller.
     *
     * @param string                 $listVariable
     * @param string                 $viewVariable
     * @param string                 $redirectRoute
     * @param string                 $searchContainerName
     * @param MapperInterface|string $mapper  The mapper instance or the mapper service name.
     * @param array                  $entityRequiredActions
     */
    public function __construct(
        $listVariable,
        $viewVariable,
        $redirectRoute,
        $searchContainerName,
        $mapper = null,
        array $entityRequiredActions = null
    ) {
        $this->listVariable        = (string) $listVariable;
        $this->viewVariable        = (string) $viewVariable;
        $this->redirectRoute       = (string) $redirectRoute;
        $this->searchContainerName = (string) $searchContainerName;

        if (null === $entityRequiredActions) {
            $entityRequiredActions = array(
                'edit',
                'view',
                'delete',
            );
        }

        parent::__construct($mapper, $entityRequiredActions);
    }

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
                $this->searchContainerName,
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
     */
    public function editAction()
    {
        $entity = $this->getEntity(true);

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
     */
    public function viewAction()
    {
        $entity = $this->getEntity(true);

        return array($this->viewVariable => $entity);
    }

    /**
     * Prompt the user to find if they want to delete an entity.
     *
     * @return array
     * @todo   Add checks that delete requests are valid.
     */
    public function deleteAction()
    {
        $entity = $this->getEntity(true);

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
