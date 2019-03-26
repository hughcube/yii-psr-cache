<?php

namespace Hughcube\YiiPsrCache\Tests;

use Hughcube\YiiPsrCache\Cache;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use yii\caching\Cache as YiiCache;

class CacheTest extends TestCase
{
    /**
     * @return Cache
     */
    public function testInstance()
    {
        /** @var YiiCache $yiiCache */
        $yiiCache = Mockery::mock(YiiCache::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $yiiCache->serializer = false;
        $yiiCache->shouldReceive('buildKey')->andReturn(__CLASS__);

        $cache = new Cache($yiiCache);

        $this->assertInstanceOf(Cache::class, $cache);
        $this->assertInstanceOf(CacheInterface::class, $cache);

        return $cache;
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testSet(Cache $cache)
    {
        $cache->getHandler()->shouldReceive('setValue')->andReturn(true);
        $this->assertSame(true, $cache->set(__CLASS__, [1]));
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testGet(Cache $cache)
    {
        $cache->getHandler()->shouldReceive('getValue')->andReturn([1]);
        $this->assertSame([1], $cache->get(__CLASS__));
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testDelete(Cache $cache)
    {
        $cache->getHandler()->shouldReceive('deleteValue')->andReturn(true);
        $this->assertSame(true, $cache->delete(__CLASS__));
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testClear(Cache $cache)
    {
        $cache->getHandler()->shouldReceive('flush')->andReturn(true);
        $this->assertSame(true, $cache->clear());
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testGetMultiple(Cache $cache)
    {
        $cache->getHandler()->shouldReceive('getValues')->andReturn([
            $cache->getHandler()->buildKey(__CLASS__) => 1
        ]);
        $this->assertSame([1 => 1, 2 => 1, 3 => 1], $cache->getMultiple([1, 2, 3]));
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testSetMultiple(Cache $cache)
    {
        $cache->getHandler()->shouldReceive('getValues')->andReturn([]);
        $this->assertSame(true, $cache->setMultiple([1 => 1, 2 => 2, 3 => 3]));
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testDeleteMultiple(Cache $cache)
    {
        $this->assertSame(true, $cache->setMultiple([__CLASS__]));
    }

    /**
     * @param Cache $cache
     * @return Cache
     * @depends testInstance
     */
    public function testHas(Cache $cache)
    {
        $cache->getHandler()->shouldReceive('exists')->andReturn(true);
        $this->assertSame(true, $cache->has(__CLASS__));
    }
}