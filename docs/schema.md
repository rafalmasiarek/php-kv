## Schema format

Internally each `(scope, id)` document is stored as JSON:

```json
{
  "schema": "v1",
  "keys": {
    "theme": {
      "schema": "v1",
      "current_version": 2,
      "created_at": "2025-01-01T12:00:00+00:00",
      "updated_at": "2025-01-02T09:00:00+00:00",
      "history": {
        "1": { "value": "dark",  "created_at": "2025-01-01T12:00:00+00:00" },
        "2": { "value": "light", "created_at": "2025-01-02T09:00:00+00:00" }
      }
    }
  }
}
```

Schemas are managed in one place: `Schema\SchemaRegistry`.

- `SchemaRegistry::CURRENT` – currently used schema id (e.g. `"v1"`).
- `SchemaRegistry::DEFINITIONS` – associative array of all supported schema versions.
- `SchemaRegistry` contains helpers used by `KV` for reading/updating documents and
  guarantees backwards compatibility: when you introduce `"v2"`, old `"v1"` docs continue
  to work and can be upgraded or appended to.

To change schema:
1. Add new entry in `SchemaRegistry::DEFINITIONS`.
2. Update `SchemaRegistry::CURRENT`.
3. Implement upgrade logic inside `SchemaRegistry` if shape changes.

## Storage backends

All backends implement `Storage\StorageInterface`:

```php
interface StorageInterface
{
    public function load(string $scope, string $id): ?array;
    public function save(string $scope, string $id, array $document): void;
    public function delete(string $scope, string $id): void;
}
```

Included:

- `FileStorage` – JSON files on disk.
- `RedisStorage` – single key per document (`kv:{scope}:{id}`).
- `MongoStorage` – one document per `(scope,id)`.

You can implement your own storage (e.g. SQL) by implementing this interface.

