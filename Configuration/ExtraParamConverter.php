<?php

namespace Bu\ExtraParamConverterBundle\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @ExtraParamConverter annotation.
 *
 * Example:
 * @ExtraParamConverter("data", jsonData=true, stripTags=true, isOptional=false, namespace="MySuperBundle", entities={
 * "user"= { class="User", name="receivingUser" }
 * })
 *
 * @author Lebedinsky Vladimir <Fludimir@gmail.com>
 * @author Irina Naydyonova <ajrina.mail@gmail.com>
 *
 * @Annotation
 */
class ExtraParamConverter extends ConfigurationAnnotation
{
    /**
     * The parameter name.
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter class.
     *
     * @var string
     */
    protected $class;

    /**
     * A flag for request data type.
     *
     * @var boolean
     */
    protected $jsonData = false;

    /**
     * A flag for stripTags action on string fields.
     *
     * @var boolean
     */
    protected $stripTags = false;

    /**
     * The project namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * An array of entities' classes names.
     *
     * @var array
     */
    protected $entities = array();

    /**
     * Use explicitly named converter instead of iterating by priorities.
     *
     * @var string
     */
    protected $converter;

    /**
     * Whether or not the parameter is optional.
     *
     * @var Boolean
     */
    protected $optional = false;

    /**
     * An array of options.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of first annotation parameter
     *
     * @param string $name The parameter name
     */
    public function setValue($name)
    {
        $this->setName($name);
    }

    /**
     * Sets the parameter name.
     *
     * @param string $name The parameter name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns a flag for request data type.
     *
     * @return boolean $jsonData
     */
    public function isJsonData()
    {
        return $this->jsonData;
    }

    /**
     * Sets a flag for request data type.
     *
     * @param boolean $isJsonData The flag value
     */
    public function setJsonData($isJsonData)
    {
        $this->jsonData = $isJsonData;
    }

    /**
     * Returns a flag for stripTags action on string fields.
     *
     * @return boolean $stripTags
     */
    public function isStripTags()
    {
        return $this->stripTags;
    }

    /**
     * Sets a flag for stripTags action on string fields.
     *
     * @param boolean $stripTags The flag value
     */
    public function setStripTags($stripTags)
    {
        $this->stripTags = $stripTags;
    }

    /**
     * Returns the project namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets the project namespace.
     *
     * @param string $namespace The namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns an array of entities.
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Sets an array of entities.
     *
     * @param array $entities An array of entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * Returns the annotation alias name.
     *
     * @return string
     * @see ConfigurationInterface
     */
    public function getAliasName()
    {
        return 'converters';
    }

    /**
     * Required because of improper interface usage in ParamConverterManager
     */
    public function getClass()
    {
    }

    /**
     * Multiple ParamConverters are not allowed.
     *
     * @return Boolean
     * @see ConfigurationInterface
     */
    public function allowArray()
    {
        return false;
    }

    /**
     * Get explicit converter name.
     *
     * @return string
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * Set explicit converter name
     *
     * @param string $converter
     */
    public function setConverter($converter)
    {
        $this->converter = $converter;
    }

    /**
     * Sets whether or not the parameter is optional.
     *
     * @param Boolean $optional Wether the parameter is optional
     */
    public function setIsOptional($optional)
    {
        $this->optional = (Boolean)$optional;
    }

    /**
     * Returns whether or not the parameter is optional.
     *
     * @return Boolean
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * Returns an array of options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets an array of options.
     *
     * @param array $options An array of options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
