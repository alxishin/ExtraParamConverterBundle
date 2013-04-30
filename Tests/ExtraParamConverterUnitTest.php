<?php

namespace Bu\ExtraParamConverterBundle;

use Bu\ExtraParamConverterBundle\Request\ParamConverter\PostConverter as Converter;
use Bu\ExtraParamConverterBundle\Tests\Fixtures\Entity;

class ExtraParamConverterUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testCallNotJsonApply()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array()));
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array());

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('request');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('data'));

        $converter = new Converter($em);
        $converter->apply($request, $config);
    }

    public function testCallJsonApply()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array());

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('[]'));

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('data'));
        $config->expects($this->once())
            ->method('isJsonData')
            ->will($this->returnValue(true));

        $converter = new Converter($em);
        $converter->apply($request, $config);
    }

    public function testNoDataNotJsonApply()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(null));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('request');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;

        $converter = new Converter($em);

        $this->setExpectedException('Bu\ExtraParamConverterBundle\Request\ParamConverter\Exception\ContentDataDoesNotValidException');
        $converter->apply($request, $config);
    }

    public function testNoDataJsonApply()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(null));

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->once())
            ->method('isJsonData')
            ->will($this->returnValue(true));

        $converter = new Converter($em);

        $this->setExpectedException('Bu\ExtraParamConverterBundle\Request\ParamConverter\Exception\ContentDataDoesNotValidException');
        $converter->apply($request, $config);
    }

    public function testNoNamespaceApply()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array()));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('request');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue(null));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ents' => 'Entity')));

        $converter = new Converter($em);

        $this->setExpectedException('Bu\ExtraParamConverterBundle\Request\ParamConverter\Exception\NamespaceDoesNotSetInAnnotationException');
        $converter->apply($request, $config);
    }

    public function testNotPostedEntsJsonApply()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('{"notents":[10,20,30]}'));

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->once())
            ->method('isJsonData')
            ->will($this->returnValue(true));
        $config->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue('App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ents' => 'Entity')));

        $converter = new Converter($em);

        $this->setExpectedException('Bu\ExtraParamConverterBundle\Request\ParamConverter\Exception\NotPostedValuesSettedInAnnotationException');
        $converter->apply($request, $config);
    }

    public function testStripTagsJsonApply()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $entity0 = new Entity;
        $entity1 = new Entity;
        $entity2 = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->at(0))
            ->method('find')
            ->with('10')
            ->will($this->returnValue($entity0));
        $repo->expects($this->at(1))
            ->method('find')
            ->with('11')
            ->will($this->returnValue($entity1));
        $repo->expects($this->at(2))
            ->method('find')
            ->with('12')
            ->will($this->returnValue($entity2));

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array(
                'ents' => array($entity0, $entity1, $entity2),
                'indata' => array('str' => 'String', 'true' => true, 'false' => false)
            ));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(
                '{"ents":["<tag>10</tag>",11,12],"indata":{"str":"<tag>String</tag>","true":true,"false":false}}'
            ));

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('data'));
        $config->expects($this->once())
            ->method('isJsonData')
            ->will($this->returnValue(true));
        $config->expects($this->once())
            ->method('isStripTags')
            ->will($this->returnValue(true));
        $config->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue('App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ents' => 'Entity')));

        $converter = new Converter($em);
        $converter->apply($request, $config);
    }

    public function testWrongIdJsonConvert()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $entity = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->any())
            ->method('find')
            ->will($this->returnValue(null));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('{"ent":100}'));

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->once())
            ->method('isJsonData')
            ->will($this->returnValue(true));
        $config->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue('App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ent' => 'Entity')));

        $converter = new Converter($em);

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            "Can't find App:Entity entity with id `100`");
        $converter->apply($request, $config);
    }

    public function testOneEntNotJsonConvert()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $entity = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('find')
            ->with(10)
            ->will($this->returnValue($entity));

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array('ent' => 10)));
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array('ent' => $entity));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('request');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name = 'data'));
        $config->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($namespace = 'App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ent' => 'Entity')));

        $converter = new Converter($em);
        $converter->apply($request, $config);

    }

    public function testOneEntJsonConvert()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $entity = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('find')
            ->with(10)
            ->will($this->returnValue($entity));

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array('ent' => $entity));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('{"ent":10}'));

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name = 'data'));
        $config->expects($this->any())
            ->method('isJsonData')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($namespace = 'App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ent' => 'Entity')));

        $converter = new Converter($em);
        $converter->apply($request, $config);
    }

    public function testManyEntsNotJsonConvert()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $entity0 = new Entity;
        $entity1 = new Entity;
        $entity2 = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->at(0))
            ->method('find')
            ->with('10')
            ->will($this->returnValue($entity0));
        $repo->expects($this->at(1))
            ->method('find')
            ->with('11')
            ->will($this->returnValue($entity1));
        $repo->expects($this->at(2))
            ->method('find')
            ->with('12')
            ->will($this->returnValue($entity2));

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array('ent' => array(10,11,12))));
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array('ent' => array($entity0, $entity1, $entity2)));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('request');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name = 'data'));
        $config->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($namespace = 'App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ent' => 'Entity')));

        $converter = new Converter($em);
        $converter->apply($request, $config);
    }

    public function testManyEntsJsonConvert()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $entity0 = new Entity;
        $entity1 = new Entity;
        $entity2 = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->at(0))
            ->method('find')
            ->with('10')
            ->will($this->returnValue($entity0));
        $repo->expects($this->at(1))
            ->method('find')
            ->with('11')
            ->will($this->returnValue($entity1));
        $repo->expects($this->at(2))
            ->method('find')
            ->with('12')
            ->will($this->returnValue($entity2));


        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array('ents' => array($entity0, $entity1, $entity2)));

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('{"ents":[10,11,12]}'));

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);;
        $config->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name = 'data'));
        $config->expects($this->any())
            ->method('isJsonData')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($namespace = 'App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ents' => 'Entity')));

        $converter = new Converter($em);
        $converter->apply($request, $config);
    }

    public function testRecursiveSearchForEntities()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $repo = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);

        $entity = new Entity;
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->any())
            ->method('find')
            ->with(10)
            ->will($this->returnValue($entity));

        $paramBag = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag', array(), array(), '', false);
        $paramBag->expects($this->once())
            ->method('all')
            ->will($this->returnValue(
                array(
                    'ent' => 10,
                    'custom' => array('ent' => 10),
                    'custom2' => array(array('ent' => array(10, 10))),
                )
        ));
        $paramBag->expects($this->once())
            ->method('set')
            ->with('data', array(
                    'ent' => $entity,
                    'custom' => array('ent' => $entity),
                    'custom2' => array(array('ent' => array($entity, $entity))),
                )
        );

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('request');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $refClass = new \ReflectionClass($request);
        $refProperty = $refClass->getProperty('attributes');
        $refProperty->setAccessible(true);
        $refProperty->setValue($request, $paramBag);

        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);
        $config->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name = 'data'));
        $config->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($namespace = 'App'));
        $config->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue(array('ent' => 'Entity')));

        $converter = new Converter($em);
        $converter->apply($request, $config);
    }

    public function testConverterSupports()
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $config = $this->getMock('Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter', array(), array(), '', false);
        $invalidConfig = $this->getMock('Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface', array(), array(), '', false);

        $converter = new Converter($em);

        $this->assertTrue($converter->supports($config));
        $this->assertFalse($converter->supports($invalidConfig));
    }
}
