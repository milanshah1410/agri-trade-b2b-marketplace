<?php

namespace App\Support;

use App\Enums\RoleName;

/**
 * Single source of truth for permissions and the default role mapping.
 *
 * The seeder is additive: it creates missing permissions and grants the defaults below,
 * but never removes permissions an administrator has customised afterwards.
 */
class PermissionCatalog
{
    /** Admin-panel modules; each gets view/create/update/delete unless listed in READ_ONLY. */
    public const MODULES = [
        'users', 'roles', 'categories', 'products', 'grades', 'pricing', 'inventory', 'warehouses',
        'customers', 'orders', 'payments', 'invoices', 'transporters', 'drivers', 'vehicles',
        'freight_quotes', 'shipments', 'tracking_events', 'refunds', 'reports', 'settings',
        'notifications', 'audit_logs',
    ];

    public const READ_ONLY = ['audit_logs'];

    public const ACTIONS = ['view', 'create', 'update', 'delete'];

    /** Special-purpose permissions beyond plain CRUD. */
    public const EXTRA = [
        'transporters.view_bank_details',
        'freight_quotes.approve',
        'refunds.approve',
        'orders.adjust_quantity',
        'inventory.adjust',
        'reports.export',
        // Customer self-service
        'orders.view_own',
        'payments.view_own',
        'invoices.view_own',
        'shipments.view_own',
        'addresses.manage_own',
        'enquiries.create',
        'support_tickets.create',
        // Transporter / driver self-service
        'shipments.view_assigned',
        'shipments.update_assigned',
        'tracking_events.create_assigned',
        'delivery_proofs.upload',
        'shipment_documents.upload',
        'freight_quotes.submit',
        'vehicles.manage_own',
        'drivers.manage_own',
        // Portal entry
        'portal.customer',
        'portal.transporter',
        'portal.driver',
    ];

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        $permissions = [];

        foreach (self::MODULES as $module) {
            foreach (self::actionsFor($module) as $action) {
                $permissions[] = "{$module}.{$action}";
            }
        }

        return array_values(array_unique(array_merge($permissions, self::EXTRA)));
    }

    /**
     * @return array<int, string>
     */
    public static function actionsFor(string $module): array
    {
        return in_array($module, self::READ_ONLY, true) ? ['view'] : self::ACTIONS;
    }

    /**
     * @return array<string, array<int, string>> role value => permission names
     */
    public static function rolePermissions(): array
    {
        $all = self::all();

        // Admin: everything except role management, user deletion, settings changes, bank details.
        $adminExcluded = [
            'roles.create', 'roles.update', 'roles.delete',
            'users.delete',
            'settings.create', 'settings.update', 'settings.delete',
            'transporters.view_bank_details',
            'portal.customer', 'portal.transporter', 'portal.driver',
            'orders.view_own', 'payments.view_own', 'invoices.view_own', 'shipments.view_own',
            'addresses.manage_own', 'enquiries.create', 'support_tickets.create',
            'shipments.view_assigned', 'shipments.update_assigned', 'tracking_events.create_assigned',
            'delivery_proofs.upload', 'shipment_documents.upload', 'freight_quotes.submit',
            'vehicles.manage_own', 'drivers.manage_own',
        ];

        return [
            RoleName::SuperAdmin->value => $all,

            RoleName::Admin->value => array_values(array_diff($all, $adminExcluded)),

            RoleName::SalesManager->value => [
                'categories.view', 'products.view', 'grades.view',
                'pricing.view', 'pricing.create', 'pricing.update',
                'customers.view', 'customers.update',
                'orders.view', 'orders.create', 'orders.update',
                'payments.view', 'invoices.view',
                'freight_quotes.view', 'freight_quotes.create', 'freight_quotes.update', 'freight_quotes.approve',
                'shipments.view', 'tracking_events.view',
                'reports.view', 'reports.export',
            ],

            RoleName::WarehouseManager->value => [
                'products.view', 'grades.view',
                'inventory.view', 'inventory.create', 'inventory.update', 'inventory.adjust',
                'warehouses.view', 'warehouses.update',
                'orders.view',
                'shipments.view', 'shipments.create', 'shipments.update',
                'tracking_events.view', 'tracking_events.create',
                'reports.view',
            ],

            RoleName::Customer->value => [
                'portal.customer',
                'orders.view_own', 'payments.view_own', 'invoices.view_own', 'shipments.view_own',
                'addresses.manage_own', 'enquiries.create', 'support_tickets.create',
            ],

            RoleName::Transporter->value => [
                'portal.transporter',
                'shipments.view_assigned', 'shipments.update_assigned',
                'tracking_events.create_assigned', 'shipment_documents.upload', 'delivery_proofs.upload',
                'freight_quotes.submit', 'vehicles.manage_own', 'drivers.manage_own',
            ],

            RoleName::Driver->value => [
                'portal.driver',
                'shipments.view_assigned', 'tracking_events.create_assigned', 'delivery_proofs.upload',
            ],
        ];
    }
}
