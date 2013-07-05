<?php

namespace SclZfSingleEntityController\Controller;

use SclZfUtilities\Mapper\GenericMapperInterface;
use Zend\Mvc\Controller\AbstractActionController as ZendActionController;

/**
 * Some common functionality for admin controllers.
 *
 * @author Tom Oram
 */
interface SingleEntityControllerInterface
{
    /**
     * Returns an array of actions which require the entity is loaded.
     *
     * @return array
     */
    public function getEntityRequiredActions();

    /**
     * Returns the mapper.
     *
     * @return GenericMapperInterface
     * @todo   Move the mapper provider trait
     */
    public function getMapper();

    /**
     * Set the current entity to be worked on.
     *
     * @param  object $entity
     * @return self
     */
    public function setEntity($entity);

    /**
     * Return the current entity being worked on.
     *
     * @return object
     */
    public function getEntity();
}
