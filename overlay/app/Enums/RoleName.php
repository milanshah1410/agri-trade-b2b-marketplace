<?php

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case SalesManager = 'sales-manager';
    case WarehouseManager = 'warehouse-manager';
    case Customer = 'customer';
    case Transporter = 'transporter';
    case Driver = 'driver';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::SalesManager => 'Sales Manager',
            self::WarehouseManager => 'Warehouse Manager',
            self::Customer => 'Customer',
            self::Transporter => 'Transporter',
            self::Driver => 'Driver',
        };
    }

    /**
     * Roles that may enter the Filament admin panel.
     *
     * @return array<int, self>
     */
    public static function staff(): array
    {
        return [self::SuperAdmin, self::Admin, self::SalesManager, self::WarehouseManager];
    }

    /**
     * @return array<int, string>
     */
    public static function staffValues(): array
    {
        return array_map(fn (self $role) => $role->value, self::staff());
    }
}
