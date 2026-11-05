<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class RbacService
{
    /**
     * Predefined system permission specifications.
     */
    public const SYSTEM_PERMISSIONS = [
        'inventory' => [
            ['slug' => 'inventory.view',        'name' => 'View Inventory & Stock'],
            ['slug' => 'inventory.manage',      'name' => 'Manage Inventory & Adjust Stock'],
            ['slug' => 'inventory.forecasting', 'name' => 'View Demand Forecasting'],
            ['slug' => 'inventory.batches',     'name' => 'Manage Batches & FIFO Expiry'],
        ],
        'pos' => [
            ['slug' => 'pos.access',            'name' => 'Access POS Cashier Terminal'],
            ['slug' => 'pos.checkout',          'name' => 'Execute POS Orders & Tender Cash'],
        ],
        'orders' => [
            ['slug' => 'orders.view',           'name' => 'View Orders List & Details'],
            ['slug' => 'orders.manage',         'name' => 'Update Order Status & Tracking'],
            ['slug' => 'orders.export',         'name' => 'Export Orders CSV'],
        ],
        'products' => [
            ['slug' => 'products.view',         'name' => 'View Products Catalog'],
            ['slug' => 'products.manage',       'name' => 'Create & Edit Products & SKUs'],
        ],
        'reports' => [
            ['slug' => 'reports.view',          'name' => 'View Financial & Tax Reports'],
            ['slug' => 'reports.export',        'name' => 'Export Reports CSV & PDF'],
        ],
        'settings' => [
            ['slug' => 'settings.manage',       'name' => 'Configure General Store Settings'],
            ['slug' => 'settings.gateways',     'name' => 'Manage Payment Gateways'],
            ['slug' => 'settings.sms',          'name' => 'Manage SMS & OTP Gateways'],
        ],
        'roles' => [
            ['slug' => 'roles.manage',          'name' => 'Manage Staff Roles & Permissions'],
        ],
    ];

    /**
     * Predefined roles and their granted permission slugs.
     */
    public const SYSTEM_ROLES = [
        'super-admin' => [
            'name'        => 'Super Admin',
            'description' => 'Full unrestricted system access to all store operations, finances, and settings.',
            'all'         => true,
        ],
        'store-manager' => [
            'name'        => 'Store Manager',
            'description' => 'Comprehensive management of inventory, orders, products, and analytics reports.',
            'permissions' => [
                'inventory.view', 'inventory.manage', 'inventory.forecasting', 'inventory.batches',
                'orders.view', 'orders.manage', 'orders.export',
                'products.view', 'products.manage',
                'reports.view', 'reports.export',
                'pos.access',
            ],
        ],
        'ecommerce-manager' => [
            'name'        => 'eCommerce Manager',
            'description' => 'Oversees digital storefront operations, online catalog merchandising, web orders fulfillment, and eCommerce analytics.',
            'permissions' => [
                'orders.view', 'orders.manage', 'orders.export',
                'products.view', 'products.manage',
                'reports.view', 'reports.export',
                'inventory.view',
                'settings.manage',
            ],
        ],
        'pos-cashier' => [
            'name'        => 'POS Cashier',
            'description' => 'In-store retail counter operator with access restricted to the POS Terminal.',
            'permissions' => [
                'pos.access', 'pos.checkout', 'products.view',
            ],
        ],
        'inventory-clerk' => [
            'name'        => 'Inventory Clerk',
            'description' => 'Warehouse specialist handling receiving, batch tracking, stock counts, and replenishment.',
            'permissions' => [
                'inventory.view', 'inventory.manage', 'inventory.forecasting', 'inventory.batches',
                'products.view',
            ],
        ],
        'customer-support' => [
            'name'        => 'Customer Support',
            'description' => 'Client assistance representative reviewing orders, shipments, and customer returns.',
            'permissions' => [
                'orders.view', 'orders.manage', 'products.view',
            ],
        ],
    ];

    /**
     * Seed or sync all default permissions and roles into database.
     */
    public function syncDefaults(): void
    {
        $permModels = [];

        foreach (self::SYSTEM_PERMISSIONS as $group => $items) {
            foreach ($items as $item) {
                $perm = Permission::firstOrCreate(
                    ['slug' => $item['slug']],
                    ['name' => $item['name'], 'group_name' => $group]
                );
                $permModels[$item['slug']] = $perm->id;
            }
        }

        foreach (self::SYSTEM_ROLES as $slug => $roleSpec) {
            $role = Role::firstOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $roleSpec['name'],
                    'description' => $roleSpec['description'],
                    'is_system'   => true,
                ]
            );

            if (!empty($roleSpec['all'])) {
                $role->permissions()->sync(array_values($permModels));
            } elseif (!empty($roleSpec['permissions'])) {
                $assignedIds = array_filter(array_map(fn($s) => $permModels[$s] ?? null, $roleSpec['permissions']));
                $role->permissions()->sync($assignedIds);
            }
        }
    }
}
