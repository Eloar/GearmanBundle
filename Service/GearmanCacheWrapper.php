<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use ReflectionClass;

use Mmoreram\GearmanBundle\Module\WorkerCollection;
use Mmoreram\GearmanBundle\Module\WorkerClass as Worker;
use Mmoreram\GearmanBundle\Driver\Gearman\Work as WorkAnnotation;

/**
 * Gearman cache loader class
 * 
 * This class has responsability of loading all gearman data structure
 * and cache it if needed.
 * 
 * Also provides this data to external services
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCacheWrapper implements CacheClearerInterface, CacheWarmerInterface
{

    /**
     * @var GearmanParser
     * 
     * Gearman file parser
     */
    private $gearmanParser;


    /**
     * @var Cache
     *
     * Cache instance
     */
    private $cache;


    /**
     * @var string
     *
     * Cache id
     */
    private $cacheId;


    /**
     * @var array
     *
     * WorkerCollection with all workers and jobs available
     */
    private $workerCollection;


    /**
     * Construct method
     *
     * @param GearmanParser $gearmanParser Gearman Parser
     * @param Cache         $cache         Cache instance
     * @param string        $cacheId       Cache id
     */
    public function __construct(GearmanParser $gearmanParser, Cache $cache, $cacheId)
    {
        $this->gearmanParser = $gearmanParser;
        $this->cache = $cache;
        $this->cacheId = $cacheId;
    }


    /**
     * Return gearman file parser
     * 
     * @return GearmanParser
     */
    public function getGearmanParser()
    {
        return $this->gearmanParser;
    }


    /**
     * Return cache
     * 
     * @return Cache Cache
     */
    public function getCache()
    {
        return $this->cache;
    }


    /**
     * Return cache id
     * 
     * @return string Cache id
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }


    /**
     * Return workerCollection
     *
     * @return array all available workers
     */
    public function getWorkers()
    {
        return $this->workerCollection;
    }


    /**
     * loads Gearman cache, only if is not loaded yet
     * 
     * @param Cache  $cache   Cache instance
     * @param string $cacheId Cache id
     *
     * @return GearmanCacheLoader self Object
     */
    public function load(Cache $cache, $cacheId)
    {
        if ($cache->contains($cacheId)) {

            /**
             * Cache contains gearman structure
             */
            $this->workerCollection = $cache->fetch($cacheId);

        } else {

            /**
             * Cache is empty.
             * 
             * Full structure must be generated and cached
             */
            $this->workerCollection = $this
                ->getGearmanParser()
                ->load()
                ->toArray();

            $cache->save($cacheId, $this->workerCollection);
        }

        return $this;
    }


    /**
     * flush all cache
     * 
     * @param Cache  $cache   Cache instance
     * @param string $cacheId Cache id
     *
     * @return GearmanCacheLoader self Object
     */
    public function flush(Cache $cache, $cacheId)
    {
        $cache->delete($cacheId);

        return $this;
    }


    /**
     * Cache clear implementation
     *
     * @param string $cacheDir The cache directory
     *
     * @return GearmanCacheLoader self Object
     */
    public function clear($cacheDir)
    {
        $this->flush($this->getCache(), $this->getCacheId());

        return $this;
    }


    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     *
     * @return GearmanCacheLoader self Object
     */
    public function warmUp($cacheDir)
    {
        $this->load($this->getCache(), $this->getCacheId());

        return $this;
    }


    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * As GearmanBundle loads cache incrementaly so is optional
     *
     * @return Boolean true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return true;
    }
}
