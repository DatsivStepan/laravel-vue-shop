<template>
    <div class="left-menu-block">
        <ul class="clearfix menu" v-if="routes">
            <left-menu-item  v-for="(route, key) in routes" :key="key" :rkey="key" :route="route"></left-menu-item>
        </ul>
    </div>
</template>

<script>
import LeftMenuItem from '@component/LeftMenuItem.vue';

export default {
    components: {
        LeftMenuItem
    },
    data() {
        return {
            routes: []
        }
    },
    mounted() {
        let app = this;
        app.getCategoriesTree();
        app.generateMenu();
    },
    computed: {
        settings: {
            get: function () {
                return this.$auth.user().settings;
            }
        }
    },
    watch: {
        settings: function () {
            let app = this;
            app.generateMenu();
            app.getCategoriesTree();
        }
    },
    methods: {
        generateMenu() {
            let app = this;
            let user = app.$auth.user();

            app.routes = [
                {
                    name: app.translate('system.menu.catalog'),
                    path: { name:'catalog' },
                    icon: 'albums',
                    show: app.checkPermission(['manageCatalog']) && (app.getSettingValue('enable_categories_tree') != 1)
                },
                {
                    name: app.translate('system.menu.my_products'),
                    path: { name: 'my-products' },
                    icon: 'cube',
                    show: app.checkPermission(['manageMyProducts']) && (app.getSettingValue('enable_my_products') == 1) && user.my_product_count
                },
                {
                    name: app.translate('system.menu.my_courses'),
                    path: { name: 'my-courses' },
                    icon: 'receipt',
                    show: app.checkPermission(['manageMyCourses']) && (app.getSettingValue('enable_my_courses') == 1) && user.my_course_count
                },
                {
                    name: app.translate('system.menu.my_orders'),
                    path: { name: 'my-orders' },
                    icon: 'document-text',
                    show: app.checkPermission(['manageMyOrders']) && (app.getSettingValue('enable_my_orders') == 1) && user.my_orders_count
                },
                {
                    name: app.translate('system.menu.my_waitlist'),
                    path: { name: 'my-waitlist' },
                    icon: 'time',
                    show: app.checkPermission(['manageWaitlist']) && (app.getSettingValue('enable_waitlist') == 1) && user.waitlist_count
                },
                {
                    name: app.translate('system.menu.my-subscriptions'),
                    path: { name: 'my-subscriptions' },
                    icon: 'time',
                    show: this.getSettingValue('enable_subscription') === '1'
                },
                {
                    name: app.translate('system.menu.sales.sales'),
                    show: app.checkPermission(['manageSales']),
                    icon: 'pricetag',
                    child: [
                        {
                            name: app.translate('system.menu.sales.all_sales'),
                            path: { name: 'admin-sales' },
                            show: app.checkPermission(['manageSales']),
                        },
                        {
                            name: app.translate('system.menu.sales.invoices'),
                            path: { name: 'admin-invoices' },
                            show: app.checkPermission(['manageSales'])
                        },
                        {
                            name: app.translate('system.menu.sales.shipping'),
                            path: { name: 'admin-shipping' },
                            show: app.checkPermission(['manageSales']) && (app.getSettingValue('enable_shipping') == 1)
                        },
                        {
                            name: app.translate('system.menu.waitlist'),
                            path: { name: 'admin-product-waitlist' },
                            show: app.checkPermission(['manageWaitlist']) && (app.getSettingValue('enable_waitlist') == 1)
                        },
                        {
                            name: app.translate('system.menu.subscriptions'),
                            path: { name: 'all-subscriptions' },
                            icon: null,
                            show: app.getSettingValue('enable_subscription') === '1'
                        },
                        {
                            name: app.translate('system.menu.sales.scheduled_enrollment'),
                            path: { name: 'admin-scheduled-enrollment' },
                            show: app.getSettingValue('display_scheduled_enrollment_list') == 1
                        },
                        {
                            name: app.translate('system.menu.sales.seats'),
                            path: { name: 'admin-seats' },
                            show: app.getSettingValue('enable_seats_vendors') == 1
                        },
                    ]
                },
                {
                    name: app.translate('system.menu.administration'),
                    show: app.checkPermission(['manageAdministration']),
                    icon: 'settings',
                    child: [
                        {
                            name: app.translate('system.menu.categories'),
                            path: { name: 'admin-categories' },
                            icon: null,
                            show: app.checkPermission(['manageCategories'])
                        },
                        {
                            name: app.translate('system.menu.products'),
                            path: { name: 'admin-products' },
                            icon: null,
                            show: app.checkPermission(['manageProducts'])
                        },
                        {
                            name: app.translate('system.menu.coupons'),
                            path: { name: 'admin-coupons' },
                            icon: null, //document-text
                            show: app.checkPermission(['manageCoupons']) && (app.getSettingValue('enable_coupons') == 1)
                        },
                        {
                            name: app.translate('system.menu.discounts'),
                            path: { name: 'admin-discounts' },
                            icon: null, //receipt
                            show: app.checkPermission(['manageDiscounts']) && (app.getSettingValue('enable_discounts') == 1)
                        },
                        {
                            name: app.translate('system.menu.vendors'),
                            path: { name: 'admin-vendors' },
                            icon: null,
                            show: app.checkPermission(['manageVendors'])
                        },
                        {
                            name: app.translate('system.menu.users'),
                            path: { name: 'admin-users' },
                            icon: null,
                            show: app.checkPermission(['manageUsers'])
                        },
                        {
                            name: app.translate('system.menu.currencies'),
                            path: { name: 'admin-currencies' },
                            icon: null,
                            show: app.checkPermission(['manageCurrencies'])
                        },
                        {
                            name: app.translate('system.menu.taxes'),
                            path: { name: 'admin-advance-taxes' },
                            icon: null,
                            show: (this.getSettingValue('enable_advance_taxes') === '1') && app.checkPermission(['manageTaxes'])
                        },
                        {
                            name: app.translate('system.menu.payments'),
                            path: { name: 'admin-payments' },
                            icon: null,
                            show: app.checkPermission(['managePayments'])
                        },
                        {
                            name: app.translate('system.menu.settings'),
                            show: app.checkPermission(['manageSettings']),
                            icon: 'settings',
                            child: [
                                {
                                    name: app.translate('system.menu.roles'),
                                    path: { name: 'admin-roles' },
                                    icon: null,
                                    show: app.checkPermission(['manageRoles'])
                                },
                                {
                                    name: app.translate('system.menu.notifications'),
                                    path: { name: 'admin-notifications' },
                                    icon: null,
                                    show: app.checkPermission(['manageNotifications'])
                                },
                                {
                                    name: app.translate('system.menu.configuration'),
                                    path: { name: 'admin-settings' },
                                    icon: null, //settings
                                    show: true
                                }
                            ]
                        }
                    ]
                }
            ];
        },
        getCategoriesTree() {
            let app = this;
            if (app.getSettingValue('enable_categories_tree') == 1) {
                axios.get('/categories/get-tree').then(response => {
                    let category = {
                        name: app.translate('system.menu.categories'),
                        path: { name: 'catalog' },
                        icon: 'albums',
                        show: app.checkPermission(['manageCategories']),
                        child: response.data
                    }

                    app.routes.unshift(category);
                }).catch(function (resp) {
                    console.error(resp);
                });
            }
        }
    }
}
</script>
