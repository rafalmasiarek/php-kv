<?php

declare(strict_types=1);

namespace rafalmasiarek\Storage;

use Redis;
use RuntimeException;

/**
 * RedisStorage
 *
 * Stores each KV document as a single JSON string in Redis:
 *   key = prefix + scope + ':' + id
 */
final class RedisStorage implements StorageInterface
{
    public function __construct(
        private Redis $redis,
        private string $prefix = 'kv:'
    ) {}

    public function load(string $scope, string $id): ?array
    {
        $raw = $this->redis->get($this->key($scope, $id));
        if ($raw === false || $raw === null) {
            return null;
        }
        $data = json_decode((string)$raw, true);
        return is_array($data) ? $data : null;
    }

    public function save(string $scope, string $id, array $document): void
    {
        $json = json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('KV JSON encode failed.');
        }
        $this->redis->set($this->key($scope, $id), $json);
    }

    public function delete(string $scope, string $id): void
    {
        $this->redis->del($this->key($scope, $id));
    }

    private function key(string $scope, string $id): string
    {
        return $this->prefix . $scope . ':' . $id;
    }
}
