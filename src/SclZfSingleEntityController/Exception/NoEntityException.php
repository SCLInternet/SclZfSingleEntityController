<?php

namespace SclZfSingleEntityController\Exception;

/**
 * Exception thrown and an entity is expected to be loaded but isn't.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class NoEntityException extends \RuntimeException implements
    ExceptionInterface
{
}
