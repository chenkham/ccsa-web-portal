<?php
declare(strict_types=1);

/**
 * File-based Cache Class
 */
class Cache
{
    /** @var string */
    private string $cacheDir;

    /** @var int */
    private int $defaultTtl;

    /**
     * Constructor
     *
     * @param string $cacheDir Directory to store cache files
     * @param int $defaultTtl Default time to live in seconds
     */
    public function __construct(string $cacheDir, int $defaultTtl = 3600)
    {
        $this->cacheDir = rtrim($cacheDir, '/\\');
        $this->defaultTtl = $defaultTtl;

        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->cacheDir));
            }
        }
    }

    /**
     * Retrieves data from the cache.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        $path = $this->getPath($key);

        if (!file_exists($path)) {
            return null;
        }

        $fp = @fopen($path, 'r');
        if ($fp) {
            flock($fp, LOCK_SH);
            $content = stream_get_contents($fp);
            flock($fp, LOCK_UN);
            fclose($fp);

            if ($content !== false && $content !== '') {
                $payload = unserialize($content);
                if (is_array($payload) && isset($payload['expiry'], $payload['data'])) {
                    if (time() < $payload['expiry']) {
                        return $payload['data'];
                    } else {
                        // Cache expired
                        $this->invalidate($key);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Saves data to the cache.
     *
     * @param string $key
     * @param mixed $data
     * @param int|null $ttl
     * @return void
     */
    public function set(string $key, mixed $data, ?int $ttl = null): void
    {
        $path = $this->getPath($key);
        $ttl = $ttl ?? $this->defaultTtl;
        $expiry = time() + $ttl;

        $payload = serialize([
            'expiry' => $expiry,
            'data'   => $data
        ]);

        $fp = @fopen($path, 'c');
        if ($fp) {
            flock($fp, LOCK_EX);
            ftruncate($fp, 0);
            fwrite($fp, $payload);
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $key
     * @return void
     */
    public function invalidate(string $key): void
    {
        $path = $this->getPath($key);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Clears all cache files.
     *
     * @return void
     */
    public function clear(): void
    {
        $files = glob($this->cacheDir . '/*');
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * Gets the full path for a cache key.
     *
     * @param string $key
     * @return string
     */
    private function getPath(string $key): string
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.cache';
    }
}
