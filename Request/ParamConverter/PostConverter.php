<?php

namespace Bu\ExtraParamConverterBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter;

/**
 * Converter class. Contains all logic for working with ExtraParamConverter.
 *
 * @author Lebedinsky Vladimir <Fludimir@gmail.com>
 * @author Irina Naydyonova <ajrina.mail@gmail.com>
 */
class PostConverter implements ParamConverterInterface
{
    protected $_em;

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $data = $configuration->isJsonData() ? json_decode($request->getContent(), true) : $request->request->all();

        if (!is_array($data)) {
            throw new Exception\ContentDataDoesNotValidException;
        }

        if ($configuration->isStripTags()) {
            //recursive walk on array to update all leaf values (all that are not arrays)
            $walkFunc = function (&$param) {
                if (is_string($param)) {
                    $param = strip_tags($param);
                }
            };
            array_walk_recursive($data, $walkFunc);
        }

        if ($entities = $configuration->getEntities()) {
            if (!($namespace = $configuration->getNamespace())) {
                throw new Exception\NamespaceDoesNotSetInAnnotationException;
            }

            $foundEntities = array();
            $converter = $this;

            $walkFunc = function (&$param, $key) use ($entities, &$foundEntities, $namespace, $converter, &$walkFunc) {
                // If key exists - we have defined class for this key and should find entity/entities
                // if not - entities data may be at deeper levels - checking by recursive search
                // Warning! $key should not be any integer value (ie array indexes)
                if (array_key_exists($key, $entities)) {
                    $class = "{$namespace}:{$entities[$key]}";
                    $param = $converter->findEntities($class, $param);
                    // counting found data to check that all that we defined in annotation is in request data
                    $foundEntities[] = $entities[$key];
                } elseif (is_array($param)) {
                    return array_walk($param, $walkFunc);
                }
            };
            array_walk($data, $walkFunc);

            if ($diff = array_diff($entities, array_unique($foundEntities))) {
                $vars = implode(',', array_keys($diff));
                $message = "Variable `$vars` was defined in annotation but not found in request";
                throw new Exception\NotPostedValuesSettedInAnnotationException($message);
            }
        }

        $request->attributes->set($configuration->getName(), $data);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return ($configuration instanceof ExtraParamConverter);
    }

    /**
     * Getting an entity by id from repository.
     *
     * @param string  $class Entity class name for searching
     * @param integer $id    Entity id for searching
     *
     * @return Entity
     */
    protected function find($class, $id)
    {
        if ($object = $this->_em->getRepository($class)->find($id)) {
            return $object;
        } else {
            throw new NotFoundHttpException("Can't find $class entity with id `$id`.");
        }
    }

    /**
     * Returns entity or array of entities, depending on param type
     * Required to be public for call from closure in php 5.3
     *
     * @param string        $class  Entity class name for searching
     * @param integer|array $idData entity id or array of ids
     *
     * @return object|array entity or array of entities
    */
    public function findEntities($class, $idData)
    {
        if (is_array($idData)) {
            $result = array();
            foreach ($idData as $id) {
                $result[] = $this->find($class, $id);
            }
        } else {
            $result = $this->find($class, $idData);
        }

        return $result;
    }
}
