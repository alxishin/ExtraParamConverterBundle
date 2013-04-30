<?php

namespace Bu\ExtraParamConverterBundle\Request\ParamConverter\Exception;

/**
 * Exception is thrown when variable is defined in `entities` annotation
 * parameter but not found in request.
 *
 * @author Irina Naydyonova <ajrina.mail@gmail.com>
 */
class NotPostedValuesSettedInAnnotationException extends Exception
{
}
