<?php

namespace Sieg\SeparatorAccess\Tests;

use PHPUnit\Framework\TestCase;
use Sieg\SeparatorAccess\DataContainer;

/**
 * Class ContainerTest
 * @package SeparatorAccess
 *
 * @covers \Sieg\SeparatorAccess\DataContainer
 */
class DataContainerTest extends TestCase
{
    public $arrayExample = [
        'key1' => 'value1',
        'key2' => 'value2',
        'key3' => [
            'key31' => 'value31',
            'key32' => 'value32'
        ],
        'key4' => [
            'key41' => [
                'key411' => 'value411',
                'key412' => 'value412'
            ]
        ]
    ];

    public function testEmptyArgumentConstructor()
    {
        $container = new DataContainer();
        $this->assertEquals([], $container->getData());
    }

    public function testConstructorWithArrayArgument()
    {
        $arrayExample = ['exampleKey' => 'exampleData'];
        $container = new DataContainer($arrayExample);
        $this->assertEquals($arrayExample, $container->getData());
    }

    public function testConstructorWithNotArrayArgument()
    {
        $notArrayExample = 'example';
        $container = new DataContainer($notArrayExample);
        $this->assertEquals([], $container->getData());
    }

    /**
     * Data provider for testGetter
     *
     * @return array
     */
    public function getterDataProvider()
    {
        $tests = [
            ['key2', 'value2'],
            ['key2.undefinedsubkey', null],
            ['key3.key32', 'value32'],
            ['undefinedkey', null],
            ['undefinedkey.secondlevel', null],
            ['undefinedkey.secondlevel.thirdlevel', null],
            ['key3.key33', null]
        ];

        return $tests;
    }

    /**
     * Testing all possible getter cases
     *
     * @dataProvider getterDataProvider
     */
    public function testGetter($key, $value)
    {
        $container = new DataContainer($this->arrayExample);
        $result = $container->get($key);
        $this->assertSame($value, $result);
    }

    public function testSetLevel0Data()
    {
        $additionalKey = 'key5';
        $additionalValue = 'value5';

        $testDataAccess = new DataContainer($this->arrayExample);
        $testDataAccess->set($additionalKey, $additionalValue);

        $this->assertArrayNotHasKey($additionalKey, $this->arrayExample);
        $this->assertEquals($testDataAccess->get($additionalKey), $additionalValue);
    }

    public function testSetLevel1Data()
    {
        $additionalKey = 'key5.key51';
        $additionalValue = 'value511';

        $testDataAccess = new DataContainer($this->arrayExample);
        $testDataAccess->set($additionalKey, $additionalValue);

        $this->assertArrayNotHasKey("key5", $this->arrayExample);
        $this->assertEquals($testDataAccess->get($additionalKey), $additionalValue);
    }

    public function testSetData()
    {
        $changedData = [
            'someKey' => 'someValue'
        ];

        $testDataAccess = new DataContainer($this->arrayExample);
        $testDataAccess->setData($changedData);

        $result = $testDataAccess->getData();
        $this->assertEquals($changedData, $result);
    }

    public function testSetDataReference()
    {
        $testDataAccess = new DataContainer();
        $testDataAccess->setDataReference($this->arrayExample);

        $testDataAccess->set("key3.key31", "othervalue31");
        $this->assertEquals($this->arrayExample, $testDataAccess->getData());
    }

    public function testResetLevel0Data()
    {
        $testData = $this->arrayExample;
        $testDataAccess = new DataContainer($this->arrayExample);

        $testDataAccess->reset('key3');
        unset($testData['key3']);

        $result = $testDataAccess->getData();
        $this->assertSame($testData, $result);
    }

    public function testResetLevel1()
    {
        $testData = $this->arrayExample;
        $testDataAccess = new DataContainer($this->arrayExample);

        $testDataAccess->reset('key3.key32');
        unset($testData['key3']['key32']);

        $result = $testDataAccess->getData();
        $this->assertSame($testData, $result);
    }

    public function testResetLevel2()
    {
        $testData = $this->arrayExample;
        $testDataAccess = new DataContainer($this->arrayExample);

        $testDataAccess->reset('key4.key41.key411');
        unset($testData['key4']['key41']['key411']);

        $result = $testDataAccess->getData();
        $this->assertSame($testData, $result);
    }
}
