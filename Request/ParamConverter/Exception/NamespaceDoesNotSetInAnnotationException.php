<?php

namespace Bu\ExtraParamConverterBundle\Request\ParamConverter\Exception;

/**
 * Exception is thrown when `entities` in the ExtraParamConverter annotation
 * is set but `namespace` is not set.
 *
 * @author Irina Naydyonova <ajrina.mail@gmail.com>
 */
class NamespaceDoesNotSetInAnnotationException extends Exception
{
    protected $message = "The `namespace` in ExtraParamConverter annotation doesn't set.";
}
