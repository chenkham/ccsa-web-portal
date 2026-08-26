<?php
declare(strict_types=1);

/**
 * File-Based Key-Value Cache
 */
class Cache
{
    private string $cacheDir;
    private int $defaultTtl;

    public function __construct(string $cacheDir, int $defaultTtl = 3600)
    {
        $this->cacheDir = rtrim($cacheDir, '/\\');
        $this->defaultTtl = $defaultTtl;

        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0755, true) && !is_dir($this->cacheDir)) {
                throw new \RuntimeException(sprintf('Cache directory "%s" could not be created.', $this->cacheDir));
            }
        }
    }

    /**
     * Retrieves cached data if present and not expired.
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
                $payload = json_decode($content, true);
                if (is_array($payload) && isset($payload['expiry'], $payload['data'])) {
                    if (time() < $payload['expiry']) {
                        return $payload['data'];
                    }
                    $this->invalidate($key);
                }
            }
        }

        return null;
    }

    /**
     * Stores data in the file cache with an expiration timestamp.
     */
    public function set(string $key, mixed $data, ?int $ttl = null): void
    {
        $path = $this->getPath($key);
        $ttl = $ttl ?? $this->defaultTtl;
        $expiry = time() + $ttl;

        $payload = json_encode([
            'expiry' => $expiry,
            'data'   => $data
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            return;
        }

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
     * Removes a specific cache key.
     */
    public function invalidate(string $key): void
    {
        $path = $this->getPath($key);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Clears all cached items.
     */
    public function clear(): void
    {
        $files = glob($this->cacheDir . '/*.cache');
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    private function getPath(string $key): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        return $this->cacheDir . DIRECTORY_SEPARATOR . $clean . '.json';
    }
}
