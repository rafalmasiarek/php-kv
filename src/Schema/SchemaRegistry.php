<?php

declare(strict_types=1);

namespace rafalmasiarek\Schema;

/**
 * SchemaRegistry
 *
 * Central definition of document/key schemas for KV.
 * All schema-specific decisions go through this class.
 *
 * Versioning rules:
 * - Each stored document has top-level "schema".
 * - Each key entry has its own "schema" so keys can migrate independently.
 * - CURRENT defines which schema is used when creating new documents/keys.
 * - When reading, unknown/missing schema falls back to CURRENT.
 *
 * This file is the only place you change when introducing e.g. v2.
 */
final class SchemaRegistry
{
    /**
     * Currently active schema id for new documents/keys.
     *
     * @var string
     */
    public const CURRENT = 'v1';

    /**
     * Supported schema definitions.
     *
     * Shape is intentionally simple so it is easy to reason about and upgrade.
     *
     * @var array<string,array<string,mixed>>
     */
    public const DEFINITIONS = [
        'v1' => [
            'id' => 'v1',
            'document' => [
                'schema' => 'v1',
                'keys'   => [], // associative: key => key-structure
            ],
            'key' => [
                'schema'          => 'v1',
                'current_version' => 0,
                'created_at'      => null,
                'updated_at'      => null,
                'history'         => [], // version => ['value' => mixed, 'created_at' => string]
            ],
        ],
    ];

    /**
     * Normalize document to a known schema.
     *
     * - Ensures "schema" is set.
     * - Ensures "keys" container exists.
     * - Does NOT touch individual key payloads (handled lazily).
     *
     * @param array<string,mixed>|null $doc
     * @return array<string,mixed>
     */
    public static function normalizeDocument(?array $doc): array
    {
        if ($doc === null) {
            return [
                'schema' => self::CURRENT,
                'keys'   => [],
            ];
        }

        $schema = isset($doc['schema']) && is_string($doc['schema'])
            ? $doc['schema']
            : self::CURRENT;

        if (!isset(self::DEFINITIONS[$schema])) {
            $schema = self::CURRENT;
        }

        if (!isset($doc['keys']) || !is_array($doc['keys'])) {
            $doc['keys'] = [];
        }

        $doc['schema'] = $schema;

        return $doc;
    }

    /**
     * Normalize a single key structure inside a document.
     *
     * - Ensures required fields exist.
     * - Keeps history as-is if present.
     *
     * @param array<string,mixed>|null $keyData
     * @param string|null              $schemaId
     * @return array<string,mixed>
     */
    public static function normalizeKey(?array $keyData, ?string $schemaId = null): array
    {
        $schema = $schemaId ?? self::CURRENT;
        if (!isset(self::DEFINITIONS[$schema])) {
            $schema = self::CURRENT;
        }

        $def = self::DEFINITIONS[$schema]['key'];

        if ($keyData === null) {
            return $def;
        }

        $out = $def;

        // Preserve known fields from existing data
        foreach (['schema', 'current_version', 'created_at', 'updated_at', 'history'] as $field) {
            if (array_key_exists($field, $keyData)) {
                $out[$field] = $keyData[$field];
            }
        }

        // Ensure schema is set
        $out['schema'] = isset($keyData['schema']) && is_string($keyData['schema'])
            ? $keyData['schema']
            : $schema;

        // Ensure history is array
        if (!isset($out['history']) || !is_array($out['history'])) {
            $out['history'] = [];
        }

        // Ensure integer current_version
        $out['current_version'] = isset($out['current_version'])
            ? (int)$out['current_version']
            : 0;

        return $out;
    }
}
