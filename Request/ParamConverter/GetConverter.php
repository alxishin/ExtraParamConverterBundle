<?php

namespace Bu\ExtraParamConverterBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;

/**
 * GetConverter automatically converts GET params to entities if they
 * are defined in action method parameters.
 *
 * @author - Lebedinsky Vladimir <Fludimir@gmail.com>
 */
class GetConverter implements ParamConverterInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();

        // If a request attribute for this name is available, we use that one
        if(true === $request->attributes->has($name))
            return false;

        if (null === $id = $request->query->get($name)) {
            $configuration->setIsOptional(true);
        }

        $object = null;

        // find by identifier?
        if ($id !== null && false === $object = $this->em->getRepository($class)->find($id)) {
            $object = null;
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        $request->attributes->set($name, $object);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return ($configuration instanceof ParamConverter);
    }

}
