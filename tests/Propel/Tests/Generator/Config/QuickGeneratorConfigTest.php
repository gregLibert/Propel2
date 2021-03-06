<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace Propel\Tests\Generator\Config;

use Propel\Generator\Config\QuickGeneratorConfig;
use Propel\Tests\TestCase;

class QuickGeneratorConfigTest extends TestCase
{
    protected $generatorConfig;

    public function setUp()
    {
        $this->generatorConfig = new QuickGeneratorConfig();
    }

    public function testGetConfiguredBuilder()
    {
        $stubTable = $this->getMock('\\Propel\\Generator\\Model\\Table');
        $actual = $this->generatorConfig->getConfiguredBuilder($stubTable, 'query');

        $this->assertInstanceOf('\\Propel\\Generator\\Builder\\Om\\QueryBuilder', $actual);
    }

    /**
     * @expectedException Propel\Generator\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid data model builder type `bad_type`
     */
    public function testGetConfiguredBuilderWrongTypeThrowsException()
    {
        $stubTable = $this->getMock('\\Propel\\Generator\\Model\\Table');
        $actual = $this->generatorConfig->getConfiguredBuilder($stubTable, 'bad_type');
    }

    public function testGetConfiguredPluralizer()
    {
        $actual = $this->generatorConfig->getConfiguredPluralizer();

        $this->assertInstanceOf('\\Propel\\Common\\Pluralizer\\StandardEnglishPluralizer', $actual);
    }

    public function testGetConfiguredPlatform()
    {
        $this->assertNull($this->generatorConfig->getConfiguredPlatform());
    }

    public function testGetBehaviorLocator()
    {
        $actual = $this->generatorConfig->getBehaviorLocator();

        $this->assertInstanceOf('\\Propel\\Generator\\Util\\BehaviorLocator', $actual);
    }

    public function testPassExtraConfigProperties()
    {
        $extraConf = [
            'propel' => [
                'database' => [
                    'connections' => [
                        'fakeConn' => [
                            'adapter' => 'sqlite',
                            'dsn' => 'sqlite:fakeDb.sqlite',
                            'user'=> '',
                            'password' => '',
                            'model_paths' => [
                                'src',
                                'vendor'
                            ]
                        ]
                    ]
                ],
                'runtime' => [
                    'defaultConnection' => 'fakeConn',
                    'connections' => ['fakeConn', 'default']
                ],
                'paths' => [
                    'composerDir' => 'path/to/composer'
                ]
            ]
        ];
        $generatorConfig = new QuickGeneratorConfig($extraConf);

        $this->assertEquals('path/to/composer', $generatorConfig->get()['paths']['composerDir']);
        $this->assertEquals('fakeConn', $generatorConfig->get()['runtime']['defaultConnection']);
        $this->assertEquals(['fakeConn', 'default'], $generatorConfig->get()['runtime']['connections']);
        $this->assertEquals(
            [
                'adapter' => 'sqlite',
                'classname' => '\Propel\Runtime\Connection\ConnectionWrapper',
                'dsn' => 'sqlite:fakeDb.sqlite',
                'user' => '',
                'password' => '',
                'model_paths' => [
                    'src',
                    'vendor'
                ]
            ],
            $generatorConfig->get()['database']['connections']['fakeConn']
        );
        $this->assertEquals(
            [
                'adapter' => 'sqlite',
                'classname' => 'Propel\Runtime\Connection\DebugPDO',
                'dsn' => 'sqlite::memory:',
                'user' => '',
                'password' => '',
                'model_paths' => [
                    'src',
                    'vendor'
                ]
            ],
            $generatorConfig->get()['database']['connections']['default']
        );
    }
}
