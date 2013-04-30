<?php

namespace Bu\ExtraParamConverterBundle;

use Bu\ExtraParamConverterBundle\Request\ParamConverter\GetConverter;
use Bu\ExtraParamConverterBundle\Tests\Fixtures\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as Configuration;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetConverterUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testCallGetApply()
    {
        $em = $this->_quickStub('Doctrine\ORM\EntityManager');
        $repo = $this->_quickStub('Doctrine\ORM\EntityRepository');

        $entity = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->with($class = 'App:Entity')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('find')
            ->with($id = 11)
            ->will($this->returnValue($entity));

        $queryBag = $this->_quickStub('Symfony\Component\HttpFoundation\ParameterBag');
        $queryBag->expects($this->once())
            ->method('get')
            ->with($name = 'entity')
            ->will($this->returnValue($id));

        $attributesBag = $this->_quickStub('Symfony\Component\HttpFoundation\ParameterBag');
        $attributesBag->expects($this->once())
            ->method('set')
            ->with($name, $entity);

        $request = $this->_quickStub('Symfony\Component\HttpFoundation\Request');
        $request->query = $queryBag;
        $request->attributes = $attributesBag;

        $config = new Configuration(array('name' => $name, 'class' => $class));;

        $converter = new GetConverter($em);
        $converter->apply($request, $config);
    }

    public function testNotFound()
    {
        $em = $this->_quickStub('Doctrine\ORM\EntityManager');
        $repo = $this->_quickStub('Doctrine\ORM\EntityRepository');

        $em->expects($this->any())
            ->method('getRepository')
            ->with($class = 'App:Entity')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('find')
            ->with($id = 12)
            ->will($this->returnValue(null));

        $queryBag = $this->_quickStub('Symfony\Component\HttpFoundation\ParameterBag');
        $queryBag->expects($this->once())
            ->method('get')
            ->with($name = 'entity')
            ->will($this->returnValue($id));

        $request = $this->_quickStub('Symfony\Component\HttpFoundation\Request');
        $request->query = $queryBag;

        $config = new Configuration(array('name' => $name, 'class' => $class));
        $converter = new GetConverter($em);

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $converter->apply($request, $config);
    }

    public function testConverterSupports()
    {
        $em = $this->_quickStub('Doctrine\ORM\EntityManager');
        $config = $this->_quickStub('Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter');
        $invalidConfig = $this->getMock('Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface');

        $converter = new GetConverter($em);

        $this->assertTrue($converter->supports($config));
        $this->assertFalse($converter->supports($invalidConfig));
    }

    protected function _quickStub($fqcn)
    {
        return $this->getMockBuilder($fqcn)
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
