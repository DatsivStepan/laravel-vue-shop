import Payments from './pages/payments/Index.vue';
import PaymentsCreate from './pages/payments/Create.vue';
import PaymentsEdit from './pages/payments/Edit.vue';
import PaymentsAssignVendors from './pages/payments/AssignVendors.vue'

let appRoutes = [
    {
        path: '/admin/payments',
        name: 'admin-payments',
        component: Payments,
        meta: {
            auth: true,
            breadcrumb: {
                label: 'system.breadcrumb.admin.payments',
            }
        }
    },
    {
        path: '/admin/payments/create',
        component: PaymentsCreate,
        name: 'admin-payments-create',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.payment_create',
                parent: 'admin-payments'
            }
        }
    },
    {
        path: '/admin/payments/edit/:id',
        component: PaymentsEdit,
        name: 'admin-payments-edit',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.payment_edit',
                parent: 'admin-payments'
            }
        }
    },
    {
        path: '/admin/payments/vendors-assigns/:id',
        component: PaymentsAssignVendors,
        name: 'admin-payments-vendors-assigns',
        meta: {
            auth: true,
            breadcrumb: {
                show: true,
                label: 'system.breadcrumb.admin.payments_vendor_assing',
                parent: 'admin-payments'
            }
        }
    },
];

export default appRoutes;
