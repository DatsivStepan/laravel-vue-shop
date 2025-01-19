<?php

namespace App\Classes\Enum;

class PermissionsNames extends AbstractEnum
{
    public const MANAGE_CATEGORIES = 'manageCategories';
    public const MANAGE_COUPONS = 'manageCoupons';
    public const MANAGE_SALES = 'manageSales';
    public const MANAGE_ROLES = 'manageRoles';
    public const MANAGE_PRODUCTS = 'manageProducts';
    public const MANAGE_TAXES = 'manageTaxes';
    public const MANAGE_SETTINGS = 'manageSettings';
    public const MANAGE_VENDORS = 'manageVendors';
    public const MANAGE_PAYMENTS = 'managePayments';
    public const MANAGE_CUSTOM_FIELDS = 'manageCustomFields';
    public const MANAGE_DISCOUNT = 'manageDiscounts';
    public const MANAGE_WAITLIST = 'manageWaitlist';
    public const MANAGE_ASSIGN_USERS = 'manageAssignUsers';
    public const MANAGE_VENDORS_RELATIONS = 'manageVendorsRelations';
    public const MANAGE_CATALOG = 'manageCatalog';
    public const MANAGE_NOTIFICATIONS = 'manageNotifications';
    public const MANAGE_CURRENCIES = 'manageCurrencies';
    public const MANAGE_MY_PRODUCTS = 'manageMyProducts';
    public const MANAGE_MY_COURSES = 'manageMyCourses';
    public const MANAGE_MY_ORDERS = 'manageMyOrders';
    public const MANAGE_USERS = 'manageUsers';
    public const MANAGE_ADMINISTRATION = 'manageAdministration';
    public const MANAGE_REVIEWS = 'manageReviews';

    public const CAN_CREATE_VENDORS = 'canCreateVendors';
    public const CAN_EDIT_VENDORS = 'canEditVendors';
    public const CAN_DELETE_VENDORS = 'canDeleteVendors';

    public const CAN_CREATE_CATEGORY = 'canCreateCategory';
    public const CAN_EDIT_CATEGORY = 'canEditCategory';
    public const CAN_DELETE_CATEGORY = 'canDeleteCategory';

    public const CAN_CREATE_PRODUCT = 'canCreateProduct';
    public const CAN_EDIT_PRODUCT = 'canEditProduct';
    public const CAN_DELETE_PRODUCT = 'canDeleteProduct';
}
