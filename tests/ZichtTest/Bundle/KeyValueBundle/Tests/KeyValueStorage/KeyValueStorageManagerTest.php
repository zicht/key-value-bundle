<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace ZichtTest\Bundle\KeyValueBundle\KeyValueStorage\Tests;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zicht\Bundle\KeyValueBundle\Entity\KeyValueStorage;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManager;
use ZichtTest\Bundle\KeyValueBundle\Tests\KeyValueStorage\FooKeysDefiner;

require 'FooKeysDefiner.php';

/**
 * Class KeyValueStorageManagerTest.
 */
class KeyValueStorageManagerTest extends WebTestCase
{

    /**
     * Test adding KeysDefiner results in keys existing with the right value.
     */
    public function testAddingKeyDefiners()
    {
        $em = $this->getEntityManager();
        $repo = $this->getRepository();
        $em->method('getRepository')->willReturn($repo);

        $manager = new KeyValueStorageManager($em);

        $definer = new FooKeysDefiner();
        $manager->addKeysDefiner($definer);

        $this->assertEquals(['foo-key', 'bar-key'], $manager->getAllKeys());
        $this->assertEquals('foo-value', $manager->getValue('foo-key'));
        $this->assertEquals('bar-value', $manager->getValue('bar-key'));
    }

    /**
     * @expectedException  \Zicht\Bundle\KeyValueBundle\KeyValueStorage\Exception\KeyAlreadyExistsException
     */
    public function testKeyAlreadyExists()
    {
        $em = $this->getEntityManager();
        $manager = new KeyValueStorageManager($em);
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

        $manager = new KeyValueStorageManager($em);

        $definer = new FooKeysDefiner();
        $manager->addKeysDefiner($definer);

        $this->assertEquals(['foo-key', 'bar-key'], $manager->getMissingDBKeys());
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

        $em = $this->getEntityManager();
        $repo = $this->getRepository();
        $repo->expects(self::at(0))->method('findOneBy')->with(['storageKey' => 'foo-key'])->willReturn($entity);
        $repo->expects(self::at(1))->method('findOneBy')->with(['storageKey' => 'bar-key'])->willReturn($entity2);
        $em->method('getRepository')->willReturn($repo);

        $manager = new KeyValueStorageManager($em);
        $manager->addKeysDefiner(new FooKeysDefiner());
        $this->assertEquals(strrev('foo-value'), $manager->getValue('foo-key'));
        $this->assertEquals(strrev('bar-value'), $manager->getValue('bar-key'));
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

        $manager = new KeyValueStorageManager($em);
        $manager->addKeysDefiner(new FooKeysDefiner());
        $this->assertEquals(strrev('foo-value'), $manager->getValue('foo-key'));
        $this->assertEquals('bar-value', $manager->getValue('bar-key'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function getEntityManager()
    {
        return $this->getMockBuilder(RegistryInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityRepository
     */
    private function getRepository()
    {
        return $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
    }

}