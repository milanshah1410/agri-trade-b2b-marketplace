<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Records created / updated / deleted events for a model in the append-only audit_logs table.
 * Override auditExcludedAttributes() to keep noisy columns out of the log.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model): void {
            AuditLog::record('created', $model, [], $model->auditPayload($model->getAttributes()));
        });

        static::updated(function (Model $model): void {
            $new = $model->auditPayload($model->getChanges());

            if ($new === []) {
                return;
            }

            $old = $model->auditPayload(Arr::only($model->getRawOriginal(), array_keys($new)));

            AuditLog::record('updated', $model, $old, $new);
        });

        static::deleted(function (Model $model): void {
            AuditLog::record('deleted', $model, $model->auditPayload($model->getAttributes()), []);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function auditPayload(array $attributes): array
    {
        $payload = Arr::except($attributes, array_merge(
            ['created_at', 'updated_at', 'remember_token'],
            $this->auditExcludedAttributes(),
        ));

        foreach ($this->auditRedactedAttributes() as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = '[redacted]';
            }
        }

        return $payload;
    }

    /**
     * @return array<int, string>
     */
    protected function auditExcludedAttributes(): array
    {
        return [];
    }

    /**
     * @return array<int, string>
     */
    protected function auditRedactedAttributes(): array
    {
        return ['password'];
    }
}
