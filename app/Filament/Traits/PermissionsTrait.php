<?php

namespace App\Filament\Traits;


trait PermissionsTrait
{

    protected static function getPermissionType(): string
    {
        throw new \Exception('The getPermissionType() method must be implemented by classes using PermissionsTrait.');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo(static::getPermissionType()) || auth()->user()->hasPermissionTo('super access');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo(static::getPermissionType()) || auth()->user()->hasPermissionTo('super access');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo(static::getPermissionType()) || auth()->user()->hasPermissionTo('super access');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo(static::getPermissionType()) || auth()->user()->hasPermissionTo('super access');
    }
}
