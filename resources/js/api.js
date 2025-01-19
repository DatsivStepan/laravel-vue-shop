import axios from 'axios';

const urlPrefixAdmin = "/admin";

export default {
    urls: {
        admin: {
            products: {
                update: `${urlPrefixAdmin}/products/`,
                options: {
                    filters: `${urlPrefixAdmin}/products/get-options/filters`
                }
            },
            customFields: {
                index: `${urlPrefixAdmin}/custom-fields`
            },
            currencies: {
                getActiveCurrencies: `${urlPrefixAdmin}/currencies/select`
            },
            users: {
                settings: {
                    setCurrency: `${urlPrefixAdmin}/users/settings/set-currency`
                }
            },
            notifications: {
                index: 'notifications/admin/templates/index',
                show: 'notifications/admin/templates/'
            },
            taxes: {
                fields: {
                    index: `${urlPrefixAdmin}/advanced-taxes/fields`,
                    store: `${urlPrefixAdmin}/advanced-taxes/fields`,
                    show: `${urlPrefixAdmin}/advanced-taxes/fields/`,
                    delete: `${urlPrefixAdmin}/advanced-taxes/fields/`
                },
                values: {
                    index: `${urlPrefixAdmin}/advanced-taxes/values`,
                    store: `${urlPrefixAdmin}/advanced-taxes/values`,
                    show: `${urlPrefixAdmin}/advanced-taxes/values/`,
                    delete: `${urlPrefixAdmin}/advanced-taxes/values/`
                }
            },
            subscriptions: {
                all: `${urlPrefixAdmin}/subscriptions`,
                my: `${urlPrefixAdmin}/subscriptions/my`,
                show: `${urlPrefixAdmin}/subscriptions/`,
                cancel: `${urlPrefixAdmin}/subscriptions/`,
                expire: `${urlPrefixAdmin}/subscriptions/`,
            },
            refunds: {
                getReasons: `${urlPrefixAdmin}/refund/reasons`
            },
        },
        catalog: {
            getProducts: `/products`,
            getMyProducts: `/products`,
            getMyFavorites: `/products`,
            getFeaturedProducts: '/products/featured',
        },
        order: {
            addToCart: '/orders/add-to-cart',
            addCoupon: '/orders/add-coupon',
            deleteCoupon: '/orders/delete-coupon',
            deleteSeatUse: '/orders/delete-seat-use'
        },
        subscribe: {
            getProduct: '/subscribe/product',
            subscribe: '/subscribe',
        }
    },

    post(url, params) {
        return axios.post(url, params);
    },
    put(url, params) {
        return axios.put(url, params);
    },
    get(url, params, config) {
        return axios.get(url, {
            params: params,
            ...config
        })
    },
    deleteRequest(url, params) {
        return axios.delete(url, {
            params: params,
        })
    },

}
