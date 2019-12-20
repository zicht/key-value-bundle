<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace ZichtTest\Bundle\KeyValueBundle\KeyValueStorage\Tests;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Zicht\Bundle\KeyValueBundle\Entity\KeyValueStorage;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\LocaleDependentData;
use ZichtTest\Bundle\KeyValueBundle\Tests\KeyValueStorage\FooKeysDefiner;
use PHPUnit\Framework\TestCase;

require 'FooKeysDefiner.php';

class KeyValueStorageManagerTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass()
    {
        // 'zz' locale as default
        LocaleDependentData::setLocale('zz');
    }

    /**
     * Test adding KeysDefiner results in keys existing with the right value.
     */
    public function testAddingKeyDefiners()
    {
        $em = $this->getEntityManager();
        $repo = $this->getRepository();
        $em->method('getRepository')->willReturn($repo);

        $manager = new KeyValueStorageManager($em, '/tmp/web', '/tmp/web/media/key_value_storage');
        $definer = new FooKeysDefiner();
        $manager->addKeysDefiner($definer);

        $this->assertEquals(['foo-key', 'bar-key', 'test-locale'], $manager->getAllKeys());
        $this->assertEquals('foo-value', $manager->getValue('foo-key'));
        $this->assertEquals('bar-value', $manager->getValue('bar-key'));
        $this->assertEquals('Garble garble', $manager->getValue('test-locale'));
    }

    /**
     * @expectedException  \Zicht\Bundle\KeyValueBundle\KeyValueStorage\Exception\KeyAlreadyExistsException
     */
    public function testKeyAlreadyExists()
    {
        $em = $this->getEntityManager();
        $manager = new KeyValueStorageManager($em, '/tmp/web', '/tmp/web/media/key_value_storage');

        $definer = new FooKeysDefiner();
        $manager->addKeysDefiner($definer);
        $manager->addKeysDefiner($definer);
    }

    /**
     * Test keys that are missing from the DB are marked.
     */
    public function testMissingDBKeys()
    {
        $em = $this->getEntityManager();
        $repo = $this->getRepository();
        $em->method('getRepository')->willReturn($repo);

        $manager = new KeyValueStorageManager($em, '/tmp/web', '/tmp/web/media/key_value_storage');

        $definer = new FooKeysDefiner();
        $manager->addKeysDefiner($definer);

        $this->assertEquals(['foo-key', 'bar-key', 'test-locale'], $manager->getMissingDBKeys());
    }

    /**
     * Test that values in the DB are returned and not the default values.
     */
    public function testValuesFromDB()
    {
        $entity = new KeyValueStorage();
        $entity->setStorageKey('foo-key');
        $entity->setStorageValue(strrev('foo-value'));

        $entity2 = new KeyValueStorage();
        $entity2->setStorageKey('bar-key');
        $entity2->setStorageValue(strrev('bar-value'));

        $entity3 = new KeyValueStorage();
        $entity3->setStorageKey('test-locale');
        $entity3->setStorageValue(['nl' => strrev('Hallo Wereld'), 'zz' => strrev('Garble garble'), 'en' => strrev('Hello World')]);

        $em = $this->getEntityManager();
        $repo = $this->getRepository();
        $repo->expects(self::at(0))->method('findOneBy')->with(['storageKey' => 'foo-key'])->willReturn($entity);
        $repo->expects(self::at(1))->method('findOneBy')->with(['storageKey' => 'bar-key'])->willReturn($entity2);
        $repo->expects(self::at(2))->method('findOneBy')->with(['storageKey' => 'test-locale'])->willReturn($entity3);
        $em->method('getRepository')->willReturn($repo);

        $manager = new KeyValueStorageManager($em, '/tmp/web', '/tmp/web/media/key_value_storage');
        $manager->addKeysDefiner(new FooKeysDefiner());
        $this->assertEquals(strrev('foo-value'), $manager->getValue('foo-key'));
        $this->assertEquals(strrev('bar-value'), $manager->getValue('bar-key'));
        $this->assertEquals(strrev('Garble garble'), $manager->getValue('test-locale'));
    }

    /**
     * Test with a mix of predefined values and DB-values.
     */
    public function testMixedValues()
    {
        $entity = new KeyValueStorage();
        $entity->setStorageKey('foo-key');
        $entity->setStorageValue(strrev('foo-value'));

        $em = $this->getEntityManager();
        $repo = $this->getRepository();
        $repo->expects(self::at(0))->method('findOneBy')->with(['storageKey' => 'foo-key'])->willReturn($entity);
        $em->method('getRepository')->willReturn($repo);

        $manager = new KeyValueStorageManager($em, '/tmp/web', '/tmp/web/media/key_value_storage');
        $manager->addKeysDefiner(new FooKeysDefiner());
        $this->assertEquals(strrev('foo-value'), $manager->getValue('foo-key'));
        $this->assertEquals('bar-value', $manager->getValue('bar-key'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ManagerRegistry
     */
    private function getEntityManager()
    {
        return $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectRepository
     */
    private function getRepository()
    {
        return $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
    }
}
