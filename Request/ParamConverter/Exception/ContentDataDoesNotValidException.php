<?php

namespace Bu\ExtraParamConverterBundle\Request\ParamConverter\Exception;

/**
 * Exception is thrown when data, that were extracted from request in the ExtraParamConverter, isn't a valid array.
 *
 * @author Irina Naydyonova <ajrina.mail@gmail.com>
 */
class ContentDataDoesNotValidException extends Exception
{
    protected $message = "The content data, that were extracted from request in ExtraParamConverter, isn't a valid array.";
}
