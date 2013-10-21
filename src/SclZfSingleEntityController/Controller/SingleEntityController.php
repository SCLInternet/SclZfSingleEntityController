<?php

namespace SclZfSingleEntityController\Controller;

use SclZfSingleEntityController\Exception\InvalidArgumentException;
use SclZfSingleEntityController\Exception\NoEntityException;
use SclZfSingleEntityController\Exception\NoMapperException;
use SclZfGenericMapper\MapperInterface;
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
     * @var MapperInterface
     * @todo Move the mapper provider trait
     */
    protected $mapper = null;

    /**
     * The current entity being worked on.
     *
     * @var object
     */
    protected $entity = null;

    /**
     * A list of actions which require the entity to be pre-loaded.
     *
     * @var array
     */
    protected $entityRequiredActions = array();

    /**
     * Set the mapper for the controller.
     *
     * @param MapperInterface|string $mapper  The mapper instance or the mapper service name.
     * @param array                  $entityRequiredActions
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
     * @return MapperInterface
     * @todo   Move the mapper provider trait
     */
    public function getMapper()
    {
        if (!$this->mapper instanceof MapperInterface && !empty($this->mapper)) {
            $this->setMapper($this->getServiceLocator()->get($this->mapper));
        }

        return $this->mapper;
    }

    /**
     * Set the mapper.
     *
     * @param  MapperInterface  $mapper
     * @return self
     * @throws InvalidArgumentException When $mapper is and object which is not an instanceof MapperInterface
     * @todo   Move the mapper provider trait
     */
    public function setMapper($mapper)
    {
        if (!$mapper instanceof MapperInterface && is_object($mapper)) {
            throw new InvalidArgumentException(
                '$mapper must be an instance of MapperInterface in '
                . __METHOD__
            );
        }

        $this->mapper = (is_object($mapper) ? $mapper : (string) $mapper);

        return $this;
    }

    /**
     * Set the current entity to be worked on.
     *
     * @param  object                   $entity
     * @return self
     * @throws NoMapperException        If the mapper has not yet been set.
     * @throws InvalidArgumentException If the entity is not of the type specified by the mapper.
     */
    public function setEntity($entity)
    {
        $mapper = $this->getMapper();

        if (null == $mapper) {
            throw new NoMapperException(
                __METHOD__ . ' was called before the mapper was set.'
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
     * @param  bool   $expected  If set to true this method will throw an
     *                           exception if the entity is not set.
     * @return object
     * @throws NoEntityException If entity is not set and $expected is true.
     */
    public function getEntity($expected = false)
    {
        if (!$expected) {
            return $this->entity;
        }

        if (!is_object($this->entity)) {
            throw new NoEntityException(__METHOD__);
        }

        return $this->entity;
    }

    /**
     * Returns an instance of the EntityFormBuilder initialised with the
     * controller's default mapper.
     *
     * @param  MapperInterface $mapper
     * @return EntityFormBuilder
     */
    public function getFormBuilder(MapperInterface $mapper = null)
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
