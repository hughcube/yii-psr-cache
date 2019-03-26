<?php

namespace Hughcube\YiiPsrCache;

use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

class Cache implements PsrCacheInterface
{
    /** @var \yii\caching\CacheInterface | \yii\cache\CacheInterface */
    protected $handler;

    /**
     * Cache constructor
     * @param \yii\caching\CacheInterface | \yii\cache\CacheInterface $handler
     */
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function set($key, $value, $ttl = null)
    {
        return true == $this->getHandler()->set($key, $value, $ttl);
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
        $handler = $this->getHandler();

        /**
         * yii 2.0.x, 2.1.x
         */
        if (method_exists($handler, 'flush')){
            return $this->getHandler()->flush();
        }

        /**
         * yii 3.x
         */
        return $this->getHandler()->clear();
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function getMultiple($keys, $default = null)
    {
        /** @var string[] $keys */

        $handler = $this->getHandler();

        /**
         * yii 2.1.x
         */
        if (method_exists($handler, 'multiGet')){
            $results = $handler->multiGet($keys);
        }//

        /**
         * yii 2.0.x
         */
        elseif (method_exists($handler, 'mget')){
            $results = $handler->mget($keys);
        }//

        /**
         * yii 3.x
         */
        else{
            $results = $handler->getMultiple($keys, $default);
        }

        foreach($keys as $key){
            if (isset($results[$key])){
                continue;
            }

            $results[$key] = $default;
        }

        return $results;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function setMultiple($values, $ttl = null)
    {
        /** @var array $values */

        $handler = $this->getHandler();

        /**
         * yii 2.1.x
         */
        if (method_exists($handler, 'multiSet')){
            return 0 >= count($handler->multiSet($values, $ttl));
        }//

        /**
         * yii 2.0.x
         */
        if (method_exists($handler, 'mset')){
            return 0 >= count($handler->mset($values, $ttl));
        }//

        /**
         * yii 3.x
         */
        return $handler->setMultiple($values, $ttl);

    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function deleteMultiple($keys)
    {
        $handler = $this->getHandler();

        /**
         * yii 2.0.x, 2.1.x
         */
        if (!method_exists($handler, 'deleteMultiple')){
            foreach($keys as $key){
                $this->delete($key);
            }

            return true;
        }

        /**
         * yii 3.x
         */
        return $handler->deleteMultiple($keys);
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
        $handler = $this->getHandler();

        /**
         * yii 2.0.x, 2.1.x
         */
        if (method_exists($handler, 'exists')){
            return true == $this->getHandler()->exists($key);
        }

        /**
         * yii 3.x
         */
        return true == $handler->has($key);
    }

    public function getHandler()
    {
        return $this->handler;
    }
}
