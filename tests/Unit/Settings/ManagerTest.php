<?php

namespace Loaf\Settings\Tests\Unit\Settings;

use Illuminate\Cache\ArrayStore;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Cache\Repository as CacheRepositoryContract;
use Illuminate\Contracts\Logging\Log as LogContract;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Loaf\Base\Contracts\Settings\ConfigElement;
use Loaf\Base\Contracts\Settings\SettingsException;
use Loaf\Base\Contracts\Settings\SettingsManager;
use Loaf\Settings\Configuration\Field;
use Loaf\Settings\Configuration\Group;
use Loaf\Settings\Configuration\Section;
use Loaf\Settings\Tests\TestCase;
use Loaf\Settings\Types\BooleanSettingType;
use Loaf\Settings\Types\IntegerSettingType;
use Loaf\Settings\Types\StringSettingType;

class ManagerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \Loaf\Settings\SettingsManager
     */
    protected $manager;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    public function setUp()
    {
        parent::setUp();
        $this->manager = $this->getManager();

        $this->manager->registerType('string', StringSettingType::class);
        $this->manager->registerType('integer', IntegerSettingType::class);
        $this->manager->registerType('boolean', BooleanSettingType::class);

        $this->manager->parseConfig();
        $this->reflection = new \ReflectionClass(\Loaf\Settings\SettingsManager::class);
    }

    public function testSetGet()
    {
        $path = 'section.group.string';

        $this->assertNull(
            $this->manager->get($path)
        );

        $this->manager->set($path, $value = 'value');

        $this->assertSame(
            $value,
            $this->manager->get($path)
        );

        $this->manager->set($path, $value = 'newvalue');

        $this->assertSame(
            $value,
            $this->manager->get($path)
        );
    }

    public function testSetGetBoolean()
    {
        $path = 'section.group.boolean';

        $this->assertNull(
            $this->manager->get($path)
        );

        $this->manager->set($path, $value = false);

        $this->assertSame(
            false,
            $this->manager->get($path)
        );
    }

    public function testGetDefault()
    {
        $default = 'default.value';
        $path = 'section.group.string';

        $this->assertSame(
            $default,
            $this->manager->get($path, $default)
        );
    }

    public function testSetForget()
    {
        $path = 'section.group.string';

        $this->manager->set($path, $value = 'value');

        $this->assertNotNull(
            $this->manager->get($path)
        );

        $this->manager->forget($path);

        $this->assertNull(
            $this->manager->get($path)
        );
    }

    public function testThrowInvalidPath()
    {
        $this->expectException(SettingsException::class);
        $this->manager->get('wrong.path');
    }

    public function testThrowNonExistentField()
    {
        $this->expectException(SettingsException::class);
        $this->manager->getField('section.group.nonexistent');
    }

    public function testThrowInvalidGroup()
    {
        $this->expectException(SettingsException::class);
        $this->manager->getSection('invalid.path.to.group');
    }

    public function testThrowNonExistentGroup()
    {
        $this->expectException(SettingsException::class);
        $this->manager->getSection('section.nonexistent');
    }

    public function testThrowNotParsed()
    {
        $manager = $this->getManager();

        // Expect an exception when not parsed
        $this->expectException(SettingsException::class);
        $manager->getField('section.group.boolean');

        // Parse the config and expect an element
        $manager->parseConfig();

        $this->assertEquals(
            Field::class,
            get_class($manager->getField('section.group.boolean'))
        );
    }

    public function testGetSections()
    {
        $sections = $this->manager->getSections();

        $this->assertArrayHasKey(
            'section',
            $sections
        );

        $section = $sections->get('section');

        $this->assertTrue(
            get_class($section) == Section::class
        );

        $this->assertTrue(
            in_array(ConfigElement::class, class_implements($section))
        );
    }

    public function testGetField()
    {
        $path = 'section.group.boolean';

        $field = $this->manager->getField($path);

        $this->assertTrue(
            get_class($field) == Field::class
        );

        $this->assertTrue(
            in_array(ConfigElement::class, class_implements($field))
        );

        $this->assertEquals(
            'boolean',
            $field->type
        );
    }

    public function testGetSettingType()
    {
        $field = $this->manager->getField('section.group.boolean');
        $type = $this->manager->getSettingType($field);
        $this->assertEquals($field->type, $type->getType());
    }

    public function testGetGroup()
    {
        $path = 'section.group';

        $group = $this->manager->getGroup($path);

        $this->assertTrue(
            get_class($group) == Group::class
        );

        $this->assertTrue(
            in_array(ConfigElement::class, class_implements($group))
        );
    }

    public function testRegisterType()
    {
        $this->manager->registerType('test-type', StringSettingType::class);

        $this->manager->mergeConfig([
            'section' => [
                'groups' => [
                    'group' => [
                        'fields' => [
                            'test-field' => [
                                'label' => 'Some test type',
                                'type'  => 'test-type',
                            ],
                        ],
                    ],
                ],
            ],
        ], true);

        $this->manager->set('section.group.test-field', $value = 'test-value');

        $this->assertEquals(
            $value,
            $this->manager->get('section.group.test-field')
        );
    }

    public function testRegisterInvalidClass()
    {
        $this->expectException(SettingsException::class);
        $this->manager->registerType('invalid', StubClass::class);
    }

    public function testCache()
    {
        // Test with a new repository with an array backend
        $cache = new \Illuminate\Cache\Repository(new ArrayStore());

        $key = $this->invokeManagerMethod('getCacheKey', ['config']);

        $this->assertFalse($cache->has($key));

        $manager = $this->getManager($cache);
        $manager->parseConfig();

        $this->assertTrue($cache->has($key));
    }

    public function testValidCache()
    {
        $cache = null;

        $this->assertFalse(
            $this->invokeManagerMethod('validConfigCache', [$cache])
        );

        $cache = ['fingerprint' => '', 'sections' => []];

        $this->assertFalse(
            $this->invokeManagerMethod('validConfigCache', [$cache])
        );

        $fingerprint = $this->invokeManagerMethod('getConfigFingerprint');

        $cache['fingerprint'] = $fingerprint;

        $this->assertTrue(
            $this->invokeManagerMethod('validConfigCache', [$cache])
        );
    }

    public function testFingerprint()
    {
        $fingerprint = $this->invokeManagerMethod('getConfigFingerprint');

        // Force a merge config
        $this->manager->mergeConfig($this->getMergeConfig(), true);

        $this->assertNotEquals(
            $this->invokeManagerMethod('getConfigFingerprint'),
            $fingerprint
        );
    }

    protected function getManager(CacheRepositoryContract $cache = null) : SettingsManager
    {
        return new \Loaf\Settings\SettingsManager(
            app(ValidationFactoryContract::class),
            new Repository($this->getConfig()),
            $cache ?? app(CacheRepositoryContract::class),
            app(LogContract::class)
        );
    }

    protected function invokeManagerMethod(string $method, array $args = [])
    {
        $method = $this->reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($this->manager, $args);
    }

    protected function getMergeConfig() : array
    {
        return [
            'general' => [
                'groups' => [
                    'website' => [
                        'fields' => [
                            'extra_field' => [
                                'label' => 'Website Name',
                                'type'  => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getConfig() : array
    {
        return $this->getTestCaseSettingsConfig();
    }
}

class StubClass
{
}
