<?php

namespace SclZfSingleEntityController\Controller;

use SclZfUtilities\Mapper\GenericMapperInterface;
use Zend\Mvc\Controller\AbstractActionController as ZendActionController;

/**
 * A generic controller which implements SingleEntityControllerInterface.
 *
 * @author Tom Oram
 * @todo   Create trait for SingleEntityControllerInterface implementation.
 */
class SingleEntityController extends ZendActionController implements
    SingleEntityControllerInterface
{
    /**
     * The mapper for loading and saving the entities that this controller works with.
     *
     * @var GenericMapperInterface
     * @todo Move the mapper provider trait
     */
    protected $mapper;

    /**
     * The current entity being worked on.
     *
     * @var object
     */
    protected $entity;

    /**
     * A list of actions which require the entity to be pre-loaded.
     *
     * @var array
     */
    protected $entityRequiredActions = array();

    /**
     * Set the mapper for the controller.
     *
     * @param GenericMapperInterface|string $mapper  The mapper instance or the mapper service name.
     * @param array                         $entityRequiredActions
     */
    public function __construct($mapper = null, array $entityRequiredActions = array())
    {
        if (null !== $mapper) {
            $this->setMapper($mapper);
        }

        $this->setEntityRequiredActions($entityRequiredActions);
    }

    /**
     * Set a list of actions which require the entity be loaded up first.
     *
     * @param  array $actions
     * @return self
     */
    public function setEntityRequiredActions(array $actions)
    {
        $this->entityRequiredActions = $actions;

        return $this;
    }

    /**
     * Returns an array of actions which require the entity is loaded.
     *
     * @return array
     */
    public function getEntityRequiredActions()
    {
        return $this->entityRequiredActions;
    }

    /**
     * Returns the mapper.
     *
     * @return GenericMapperInterface
     * @todo   Move the mapper provider trait
     */
    public function getMapper()
    {
        if (!$this->mapper instanceof GenericMapperInterface) {
            $this->setMapper($this->getServiceLocator()->get($this->mapper));
        }

        return $this->mapper;
    }

    /**
     * Set the mapper.
     *
     * @param  GenericMapperInterface $mapper
     * @return self
     * @todo   Move the mapper provider trait
     */
    public function setMapper($mapper)
    {
        if (!$mapper instanceof GenericMapperInterface && is_object($mapper)) {
            throw new InvalidArgumentException(
                '$mapper must be an instance of GenericMapperInterface in '
                . __METHOD__
            );
        }

        $this->mapper = (is_object($mapper) ? $mapper : (string) $mapper);

        return $this;
    }

    /**
     * Set the current entity to be worked on.
     *
     * @param  object $entity
     * @return self
     */
    public function setEntity($entity)
    {
        $mapper = $this->getMapper();

        if (!$mapper instanceof GenericMapperInterface) {
            throw new RuntimeException(
                'setEntity was called before the mapper was set.'
            );
        }

        if (!is_object($entity) || !is_a($entity, $this->getMapper()->getEntityName())) {
            throw new InvalidArgumentException(
                '$entity must be and instance of '
                . $this->getMapper()->getEntityName()
                . ' in ' . __METHOD__
            );
        }

        $this->entity = $entity;

        return $this;
    }

    /**
     * Return the current entity being worked on.
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Returns an instance of the EntityFormBuilder initialised with the
     * controller's default mapper.
     *
     * @param  GenericMapperInterface $mapper
     * @return EntityFormBuilder
     */
    public function getFormBuilder(GenericMapperInterface $mapper = null)
    {
        $plugin = $this->plugin('getFormBuilder');

        $mapper = (null === $mapper) ? $this->getMapper() : $mapper;

        return $plugin($mapper);
    }

    /**
     * Redirect to the given route.
     *
     * @param  string   $route
     * @param  array    $params
     * @return Response
     */
    protected function redirectToRoute($route, $params = array())
    {
        return $this->redirect()->toRoute($route, $params);
    }
}
