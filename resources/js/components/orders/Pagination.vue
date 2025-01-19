<template>

    <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
        <router-link v-if='!pagination.prev' :to="{name: 'catalog'}" class="d-block mb-3 mr-3 mb-sm-0">
            <b-icon icon="arrow-left" font-scale="1"></b-icon>
            {{translate('system.back')}}
        </router-link>

        <a class="d-block mb-3 mb-sm-0" v-if='pagination.prev' @click="$emit('minus-tab', pagination.prev.toTab)">
            <b-icon icon="arrow-left" font-scale="1"></b-icon>
            {{pagination.prev.title}}
        </a>

        <slot name="payment-button"></slot>
        <button type="button" class="btn btn-success" v-if='pagination.next' @click="$emit('plus-tab', pagination.next.toTab)">
            {{pagination.next.title}}
        </button>
    </div>

</template>

<script>
export default {
    props: ['tabs', 'in'],
    data: function () {
        return {
            pagination: {
                prev: null,
                next: null,
            },
       }
    },
    mounted() {
        let app = this;
        if (app.tabs) {
            let tabsCount = app.tabs.length;
            let indexTab = app.tabs.findIndex((tab) => tab.contentKey == app.in);

            if (indexTab != 0) {
                app.pagination.prev = {
                    title: app.translate('system.back'),
                    toTab: indexTab-1
                }
            }

            if (indexTab != (tabsCount-1)) {
                let nexTitle = app.tabs[indexTab].hasOwnProperty('nextButtonTitle') ?
                    app.tabs[indexTab].nextButtonTitle : app.translate('system.continue');

                app.pagination.next = {
                    title: nexTitle,
                    toTab: indexTab+1
                }
            }
        }
    },
    methods: {
        toTab(tab) {
            let app = this;
            app.$store.commit('setActiveCartTab', app.tabs[tab].contentKey);
        },
    }
}
</script>
