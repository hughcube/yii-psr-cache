<?php

namespace Hughcube\YiiPsrCache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;
use yii\caching\Cache as YiiCache;
use yii\di\Instance;

class Cache implements PsrCacheInterface
{
    /** @var YiiCache */
    protected $handler;

    /**
     * Cache constructor
     * @param YiiCache $handler
     */
    public function __construct(YiiCache $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->getHandler()->set($key, $value, $ttl);
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function get($key, $default = null)
    {
        $data = $this->getHandler()->get($key);

        return false == $data ? $default : $data;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function clear()
    {
        return $this->getHandler()->flush();
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function getMultiple($keys, $default = null)
    {
        /** @var string[] $keys */

        $results = $this->getHandler()->multiGet($keys);

        foreach($results as $key => $result){
            if (empty($result)){
                $results[$key] = $default;
            }
        }

        return $results;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function setMultiple($values, $ttl = null)
    {
        $failedKeys = $this->getHandler()->multiSet($values, $ttl);

        return empty($failedKeys);
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function deleteMultiple($keys)
    {
        foreach($keys as $key){
            $this->delete($key);
        }

        return true;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function delete($key)
    {
        return false !== $this->getHandler()->delete($key);
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function has($key)
    {
        return true == $this->getHandler()->exists($key);
    }

    /**
     * @return YiiCache
     * @throws \yii\base\InvalidConfigException
     */
    public function getHandler()
    {
        if (!$this->handler instanceof YiiCache){
            $this->handler = Instance::ensure($this->handler, YiiCache::class);
        }

        return $this->handler;
    }
}
