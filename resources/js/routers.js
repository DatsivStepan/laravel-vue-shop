import VueRouter from 'vue-router'

// auth
import Login from './pages/auth/Login.vue'

//Module Payment
import ModulePaymentRoutes from '@modules/Payments/Resources/js/routers.js';

//ADMIN//
import AdminDashboard from './pages/admin/Dashboard.vue';
//products
import Products from './pages/admin/products/Index.vue';
import ProductsCreate from './pages/admin/products/Create.vue';
import ProductsEdit from './pages/admin/products/Edit.vue';
import AssignCourseIndex from './pages/admin/products/AssignCourse.vue';
import ProductsAssignVendors from './pages/admin/products/AssignVendors.vue';
import ProductsWaitlist from './pages/admin/products/Waitlist.vue';
//categories
import Categories from './pages/admin/categories/Index.vue';
import CategoriesCreate from './pages/admin/categories/Create.vue';
import CategoriesEdit from './pages/admin/categories/Edit.vue';
import CategoriesAssignVendors from './pages/admin/categories/AssignVendors.vue';
// coupons
import Coupons from './pages/admin/coupons/Index.vue';
import CouponsCreate from './pages/admin/coupons/Create.vue';
import CouponsEdit from './pages/admin/coupons/Edit.vue';
import CouponAssignVendors from './pages/admin/coupons/AssignVendors.vue';
import CouponAssignProducts from './pages/admin/coupons/AssignProduct.vue';

// discount
import Discount from './pages/admin/discounts/Index.vue';
import DiscountCreate from './pages/admin/discounts/Create.vue';
import DiscountEdit from './pages/admin/discounts/Edit.vue';
import DiscountAssignVendors from './pages/admin/discounts/AssignVendors.vue';
import DiscountAssignProducts from './pages/admin/discounts/AssignProduct.vue';

//advance-taxes
import AdvanceTaxCreate from './pages/admin/taxes/fields/Create.vue';
import AdvanceTaxFieldIndex from './pages/admin/taxes/fields/Index.vue';
import AdvanceTaxValueIndex from './pages/admin/taxes/values/Index.vue';
import AdvanceTaxValueCreate from './pages/admin/taxes/values/Create.vue';

//currencies
import Currencies from './pages/admin/currencies/Index.vue';
import CurrencyCreate from './pages/admin/currencies/Create.vue';
import CurrencyEdit from './pages/admin/currencies/Edit.vue';

//notifications
import Notifications from './pages/admin/notifications/Index.vue';
import NotificationsEdit from './pages/admin/notifications/Edit.vue';

//subscriptions
import SubscribeProduct from './pages/subscriptions/Subscription.vue';
import AllSubscriptions from './pages/admin/subscriptions/AllSubscriptions.vue';

//roles
import Roles from './pages/admin/roles/Index.vue';
import RolesLms from './pages/admin/roles/AssignLmsRole.vue';
import RolesEdit from './pages/admin/roles/Edit.vue';
import Permissions from './pages/admin/roles/Permissions.vue';

//settings
import Settings from './pages/admin/settings/Index.vue';
import InvoiceTemplate from './pages/admin/settings/InvoiceTemplate.vue';

//sales
import SalesIndex from './pages/admin/sales/Index.vue';
import InvoicesIndex from './pages/admin/sales/Invoices.vue';
import ShippingIndex from './pages/admin/sales/Shipping.vue';
import ShippingScheduledEnrollment from './pages/admin/sales/ScheduledEnrollment.vue';
import SeatsIndex from './pages/admin/sales/Seats.vue';
import SeatsDetailsIndex from './pages/admin/sales/SeatsDetails.vue';

//vendors
import Vendors from './pages/admin/vendors/Index.vue';
import VendorsCreate from './pages/admin/vendors/Create.vue';
import VendorsEdit from './pages/admin/vendors/Edit.vue';
import VendorsUsers from './pages/admin/vendors/AssignUsers.vue';

//vendors
import Users from './pages/admin/users/Index.vue';

//custom fields
import CustomFields from './pages/admin/custom_field/Index.vue';
import CustomFieldsCreate from './pages/admin/custom_field/Create.vue';
import CustomFieldsEdit from './pages/admin/custom_field/Edit.vue';
//ADMIN//
//
//FRONTEND//
import Catalog from './pages/products/Catalog.vue';
import MyProducts from './pages/products/MyProducts.vue';
import ProductShow from './pages/products/Show.vue';
import MyCourses from './pages/courses/MyCourses.vue';
import MySubscriptions from './pages/admin/subscriptions/MySubscriptions.vue';
import MyWaitList from './pages/products/MyWaitlist.vue';
import MyFavorites from './pages/products/MyFavorites.vue';
//CHECKOUT//
import Checkout from './pages/orders/Checkout.vue';
import CheckoutEdit from './pages/orders/CheckoutEdit.vue';
import Refund from './pages/orders/Refund.vue';
import MyOrders from './pages/orders/MyOrders.vue';
import CheckoutComplete from './pages/orders/CheckoutComplete.vue';
//CHECKOUT//
//FRONTEND//
import NotFound from './pages/NotFound.vue';

import Stripe from "@component/billingType/Stripe";
import Close from "@component/billingType/Close";


let appRoutes = [
    {
        path: '/payments/stripe',
        name: 'Stripe',
        component: Stripe,
        props: true
    },
    {
        path: '*',
        component: NotFound,
    },
    {
        path: '/payments/close',
        name: 'Close',
        component: Close,
        props: true,
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
    },

    // ADMIN //
    //products
    {
        path: '/admin/products',
        name: 'admin-products',
        component: Products,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.products',
            }
        }
    },
    {
        path: '/admin/products/create',
        component: ProductsCreate,
        name: 'admin-product-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.product_create',
                parent: 'admin-products'
            }
        }
    },
    {
        path: '/admin/products/edit/:id',
        component: ProductsEdit,
        name: 'admin-product-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.product_edit',
                parent: 'admin-products'
            }
        }
    },
    {
        path: '/admin/products/assign-course/:id',
        name: 'admin-product-assign-course',
        component: AssignCourseIndex,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.assing_course',
                parent: 'admin-products'
            }
        }
    },
    {
        path: '/admin/products/vendors-assigns/:id',
        component: ProductsAssignVendors,
        name: 'admin-product-vendors-assigns',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.assing_vendor',
                parent: 'admin-products'
            }
        }
    },
    {
        path: '/admin/products/waitlist',
        component: ProductsWaitlist,
        name: 'admin-product-waitlist',
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.waitlist',
            }
        }
    },
    //categories
    {
        path: '/admin/categories/:id?',
        name: 'admin-categories',
        component: Categories,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.categories',
            }
        }
    },
    {
        path: '/admin/categories/create',
        component: CategoriesCreate,
        name: 'admin-categories-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.categories_create',
                parent: 'admin-categories'
            }
        }
    },
    {
        path: '/admin/categories/edit/:id',
        component: CategoriesEdit,
        name: 'admin-categories-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.categories_edit',
                parent: 'admin-categories'
            }
        }
    },
    {
        path: '/admin/categories/vendors-assigns/:id',
        component: CategoriesAssignVendors,
        name: 'admin-categories-vendors-assigns',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.category_vendor_assing',
                parent: 'admin-categories'
            }
        }
    },

    // coupons
    {
        path: '/admin/coupons',
        name: 'admin-coupons',
        component: Coupons,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.coupons',
            }
        }
    },
    {
        path: '/admin/coupons/create',
        component: CouponsCreate,
        name: 'admin-coupons-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.coupons_create',
                parent: 'admin-coupons'
            }
        }
    },
    {
        path: '/admin/coupons/edit/:id',
        component: CouponsEdit,
        name: 'admin-coupons-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.coupons_edit',
                parent: 'admin-coupons'
            }
        }
    },
    {
        path: '/admin/coupons/vendors-assigns/:id',
        component: CouponAssignVendors,
        name: 'admin-coupons-vendors-assigns',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.coupons_assing_vendor',
                parent: 'admin-coupons'
            }
        }
    },
    {
        path: '/admin/discounts/products-assigns/:id',
        component: CouponAssignProducts,
        name: 'admin-coupons-products-assigns',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.coupons_assing_products',
                parent: 'admin-coupons'
            }
        }
    },

    // discounts
    {
        path: '/admin/discounts',
        name: 'admin-discounts',
        component: Discount,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.discounts',
            }
        }
    },
    {
        path: '/admin/discounts/create',
        component: DiscountCreate,
        name: 'admin-discount-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.discounts_create',
                parent: 'admin-discounts'
            }
        }
    },
    {
        path: '/admin/discounts/edit/:id',
        component: DiscountEdit,
        name: 'admin-discounts-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.discounts_edit',
                parent: 'admin-discounts'
            }
        }
    },
    {
        path: '/admin/discounts/vendors-assigns/:id',
        component: DiscountAssignVendors,
        name: 'admin-discounts-vendors-assigns',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.discounts_vendor_assing',
                parent: 'admin-discounts'
            }
        }
    },
    {
        path: '/admin/discounts/products-assigns/:id',
        component: DiscountAssignProducts,
        name: 'admin-discounts-products-assigns',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.discounts_product_assing',
                parent: 'admin-discounts'
            }
        }
    },

    //Subscription
    {
        path: '/subscribe/product',
        component: SubscribeProduct,
        name: 'subscribe-product',
        props: true,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.subscribe_product',
            }
        }
    },
    {
        path: 'admin/subscriptions',
        component: AllSubscriptions,
        name: 'all-subscriptions',
        props: true,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.all_subscriptions',
            }
        }
    },
    {
        path: 'admin/my-subscriptions',
        component: MySubscriptions,
        name: 'my-subscriptions',
        props: true,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.my_subscriptions',
            }
        }
    },

    //AdvanceTaxCreate
    {
        path: '/admin/advance-taxes',
        component: AdvanceTaxFieldIndex,
        name: 'admin-advance-taxes',
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.taxes',
            }
        }
    },
    {
        path: '/admin/advance-taxes/create',
        component: AdvanceTaxCreate,
        name: 'admin-advance-taxes-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.taxes_create',
                parent: 'admin-advance-taxes'
            }
        }
    },
    {
        path: '/admin/advance-taxes/values/:field_id',
        component: AdvanceTaxValueIndex,
        name: 'admin-advance-taxes-values',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.taxes_values',
                parent: 'admin-advance-taxes'
            }
        }
    },
    {
        path: '/admin/advance-taxes/values/create',
        component: AdvanceTaxValueCreate,
        name: 'admin-advance-taxes-values-create',
        props: true,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.taxes_values_create',
                parent: 'admin-advance-taxes'
            }
        }
    },

    //Currency
    {
        path: '/admin/currencies',
        name: 'admin-currencies',
        component: Currencies,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.currencies',
            }
        }
    },
    {
        path: '/admin/currencies/add',
        name: 'admin-currencies-add',
        component: CurrencyCreate,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.currencies_add',
                parent: 'admin-currencies'
            }
        }
    },
    {
        path: '/admin/currencies/edit',
        name: 'admin-currencies-edit',
        component: CurrencyEdit,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.currencies_edit',
                parent: 'admin-currencies'
            }
        }
    },

    //Notifications
    {
        path: '/admin/notifications',
        name: 'admin-notifications',
        component: Notifications,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.notifications',
            }
        }
    },
    {
        path: '/admin/notifications/edit',
        name: 'admin-notifications-edit',
        component: NotificationsEdit,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.notifications_edit',
                parent: 'admin-notifications'
            }
        }
    },

    //dashboard
    {
        path: '/admin/dashboard',
        name: 'admin-dashboard',
        component: AdminDashboard,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.dashboard',
            }
        }
    },
    //roles
    {
        path: '/admin/roles',
        name: 'admin-roles',
        component: Roles,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.roles',
            }
        }
    },
    {
        path: '/admin/roles/assign-lms-role/:id',
        name: 'admin-assigns-lms-roles',
        component: RolesLms,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.roles_assing_lms',
                parent: 'admin-roles'
            }
        }
    },
    {
        path: '/admin/roles/edit/:id',
        component: RolesEdit,
        name: 'admin-roles-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.roles_edit',
                parent: 'admin-roles'
            }
        }
    },
    {
        path: '/admin/roles/show/:id',
        component: Permissions,
        name: 'admin-roles-show',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.roles_show',
                parent: 'admin-roles'
            }
        }
    },
    //settings
    {
        path: '/admin/settings',
        name: 'admin-settings',
        component: Settings,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.settings',
            }
        }
    },
    {
        path: '/admin/invoice-template',
        name: 'admin-invoice-template',
        component: InvoiceTemplate,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.invoice_template',
            }
        }
    },
    //Sales
    {
        path: '/admin/sales',
        name: 'admin-sales',
        component: SalesIndex,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.sales',
            }
        }
    },
    {
        path: '/admin/invoices',
        name: 'admin-invoices',
        component: InvoicesIndex,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.invoices',
            }
        }
    },
    {
        path: '/admin/shipping',
        name: 'admin-shipping',
        component: ShippingIndex,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.shipping',
            }
        }
    },
    {
        path: '/admin/scheduled-enrollment',
        name: 'admin-scheduled-enrollment',
        component: ShippingScheduledEnrollment,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.scheduled_enrollment',
            }
        }
    },
    {
        path: '/admin/seats',
        name: 'admin-seats',
        component: SeatsIndex,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.seats',
            }
        }
    },
    {
        path: '/admin/seats/details/:id',
        name: 'admin-order-seats-details',
        component: SeatsDetailsIndex,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.seats_details',
                parent: 'admin-seats'
            }
        }
    },

    // Users
    {
        path: '/admin/users',
        name: 'admin-users',
        component: Users,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.users',
            }
        }
    },

    // Vendors
    {
        path: '/admin/vendors',
        name: 'admin-vendors',
        component: Vendors,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.vendors',
            }
        }
    },
    {
        path: '/admin/vendors/create',
        component: VendorsCreate,
        name: 'admin-vendors-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.vendors_create',
                parent: 'admin-vendors'
            }
        }
    },
    {
        path: '/admin/vendors/edit/:id',
        component: VendorsEdit,
        name: 'admin-vendors-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.vendors_edit',
                parent: 'admin-vendors'
            }
        }
    },
    {
        path: '/admin/vendors-users',
        name: 'admin-vendors-users',
        component: VendorsUsers,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.vendors_users',
                parent: 'admin-vendors'
            }
        }
    },
    //custom fields
    {
        path: '/admin/custom-fields/:instanceType',
        name: 'admin-custom-fields',
        component: CustomFields,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.custom_fields',
                parent: 'admin-products'
            }
        }
    },
    {
        path: '/admin/custom-fields/create/:instanceType',
        component: CustomFieldsCreate,
        name: 'admin-custom-fields-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.custom_fields_create',
                parent: 'admin-custom-fields'
            }
        }
    },
    {
        path: '/admin/custom-fields/edit/:id',
        component: CustomFieldsEdit,
        name: 'admin-custom-fields-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.custom_fields_edit',
                parent: 'admin-custom-fields'
            }
        }
    },
    // ADMIN //

    // FRONTEND //
    {
        path: '/:id?',
        name: 'catalog',
        component: Catalog,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.catalog'
            }
        },
    },
    {
        path: '/my-products',
        name: 'my-products',
        component: MyProducts,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.my_products',
            }
        }
    },
    {
        path: '/my-favorites',
        name: 'my-favorites',
        component: MyFavorites,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.my_favorites',
            }
        }
    },
    {
        path: '/my-courses',
        name: 'my-courses',
        component: MyCourses,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.my_courses',
            }
        }
    },
    {
        path: '/my-waitlist/index',
        name: 'my-waitlist',
        component: MyWaitList,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.my_waitlist',
            }
        }
    },
    {
        path: '/my-orders',
        name: 'my-orders',
        component: MyOrders,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.my_orders',
            }
        }
    },
    {
        path: '/products/show/:id',
        component: ProductShow,
        name: 'product-show',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.product_show',
                parent: 'catalog'
            }
        }
    },
    {
        path: '/products/show/:id',
        component: ProductShow,
        name: 'product-show-my',
        props: true,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.product_show',
                parent: 'my-products'
            }
        }
    },
    {
        path: '/checkout/index',
        component: Checkout,
        name: 'checkout',
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.checkout',
            }
        }
    },
    {
        path: '/checkout/edit',
        component: CheckoutEdit,
        name: 'checkout-edit',
        props: true,
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.checkout_edit',
                parent: 'admin-invoices'
            }
        }
    },
    {
        path: '/checkout/complete',
        component: CheckoutComplete,
        name: 'checkout-complete',
        props: true,
        meta: {
            auth: true,
            breadcrumb: false
        }
    },

    //Refund
    {
        path: '/refund',
        component: Refund,
        name: 'refund',
        props: true,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.refund',
            }
        }
    },
    // FRONTEND //
];

if (ModulePaymentRoutes) {
    appRoutes = appRoutes.concat(ModulePaymentRoutes);
}

export default new VueRouter({
    history: true,
    mode: 'history',
    routes: appRoutes
});
