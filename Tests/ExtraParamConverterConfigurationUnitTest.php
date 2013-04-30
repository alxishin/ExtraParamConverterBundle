<?php

namespace Bu\ExtraParamConverterBundle;

use Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter;

class ExtraParamConverterConfigurationUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testMinimalConfiguration()
    {
        $configuration = new ExtraParamConverter(array(
            'name'      => $name = 'configName',
            'namespace' => $namespace = 'MySuperBundle'
        ));

        $this->assertEquals($configuration->getName(), $name);
        $this->assertEquals($configuration->getNamespace(), $namespace);
        $this->assertEquals($configuration->getEntities(), array());
        $this->assertFalse($configuration->isJsonData());
        $this->assertFalse($configuration->isStripTags());

        $this->assertFalse($configuration->allowArray());

        $configuration->setValue($nameValue = 'data');
        $this->assertEquals($configuration->getName(), $nameValue);

        $this->assertNull($configuration->getClass());

        $this->assertEquals('converters', $configuration->getAliasName());

        $configuration->setConverter('ExtraParamConverter');
        $this->assertEquals('ExtraParamConverter', $configuration->getConverter());
    }

    public function testFullConfiguration()
    {
        $entities = array(
            'users'    => 'User',
            'features' => 'Feature'
        );

        $configuration = new ExtraParamConverter(array(
            'name'      => $name = 'data',
            'namespace' => $namespace = 'MySuperBundle',
            'entities'  => $entities,
            'jsonData'  => true,
            'stripTags' => true,
        ));

        $this->assertEquals($configuration->getName(), $name);
        $this->assertTrue($configuration->isJsonData());
        $this->assertTrue($configuration->isStripTags());
        $this->assertEquals($configuration->getNamespace(), $namespace);
        $this->assertEquals($configuration->getEntities(), $entities);
    }
}
