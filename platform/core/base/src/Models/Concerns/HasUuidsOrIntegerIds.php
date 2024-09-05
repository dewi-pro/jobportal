<?php

namespace Botble\Base\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuidsOrIntegerIds
{
    public static function bootHasUuidsOrIntegerIds(): void
    {
        static::creating(static function (self $model): void {
            if (self::getTypeOfId() === 'BIGINT') {
                return;
            }

            $model->{$model->getKeyName()} = $model->newUniqueId();
        });
    }

    public function newUniqueId(): string|null
    {
        return match (self::getTypeOfId()) {
            'ULID' => (string)Str::ulid(),
            'BIGINT' => null,
            default => (string)Str::orderedUuid(),
        };
    }

    public function getKeyType(): string
    {
        if (self::getTypeOfId() !== 'BIGINT') {
            return 'string';
        }

        return $this->keyType;
    }

    public function getIncrementing(): bool
    {
        if (self::getTypeOfId() !== 'BIGINT') {
            return false;
        }

        return $this->incrementing;
    }

    public static function determineIfUsingUuidsForId(): bool
    {
        return self::getTypeOfId() === 'UUID';
    }

    public static function determineIfUsingUlidsForId(): bool
    {
        return self::getTypeOfId() === 'ULID';
    }

    public static function getTypeOfId(): string
    {
        if (config('core.base.general.using_uuids_for_id', false)) {
            return 'UUID';
        }

        if (config('core.base.general.using_ulids_for_id', false)) {
            return 'ULID';
        }

        return strtoupper(config('core.base.general.type_id', 'BIGINT'));
    }
}
