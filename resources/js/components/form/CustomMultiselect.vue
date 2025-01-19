<template>
    <div class="custom-multiselect">
        <multiselect
            v-model="selectedItem"
            :options="sItems"
            :multiple="configSelect.multiple"
            :close-on-select="configSelect.closeOnSelect"
            :clear-on-select="false"
            :selectLabel="''"
            :selectedLabel="''"
            :deselectLabel="''"
            :options-limit="configSelect.optionsLimit"
            :preserve-search="true"
            :loading="isLoading"
            label="name"
            track-by="name"
            :preselect-first="true"
            @search-change="searchItems"
            @open="searchItems"
            :placeholder="placeholder || 'Select Option'"
        >
            <template slot="selection" slot-scope="{ values, search, isOpen }">
                <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected</span>
            </template>

            <template slot="option" slot-scope="props">
                <b-icon icon="circle-fill" v-if="!configSelect.multiple && selected(props.option.value)"></b-icon>
                <b-icon icon="circle" v-if="!configSelect.multiple && !selected(props.option.value)"></b-icon>

                <b-icon icon="check-square-fill" v-if="configSelect.multiple && selected(props.option.value)"></b-icon>
                <b-icon icon="app" v-if="configSelect.multiple && !selected(props.option.value)"></b-icon>
                {{ props.option.name }}
            </template>

            <!-- CHECKBOXES FOR SECOND SELECT FORM -->

            <!-- <template slot="option" slot-scope="props">
                <b-icon icon="check-square" v-if="selected(props.option.value)"></b-icon>
                <b-icon icon="square" v-if="!selected(props.option.value)"></b-icon>
                {{ props.option.name }}
            </template> -->
        </multiselect>
        <div v-if="configSelect.selectAll.show && sItems.length">
            <input type="checkbox" v-model='selectAll' @change="mSelectAll">
            <label>{{ configSelect.selectAll.title }}</label>
        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import '@sass/component/table.scss'

export default {
    props: {
        searchData: {
            type: Object,
            default: () => ({})
        },
        items: {
            type: Array,
            default: () => ([])
        },
        selectedItems: [Object, Array, Number],
        config: {
            type: Object,
            default: () => ({})
        },
        placeholder: {
            type: String,
            default: null
        },
    },
    components: {
        Multiselect
    },
    data () {
        let app = this;
        return {
            sItems: app.items,
            isLoading: false,
            selectedItem: null,
            selectAll: null,
            defaultConfig: {
                selectAll: {
                    show: false,
                    title: app.translate('system.select_all')
                },
                mountedSearch: false,
                multiple: true,
                optionsLimit: 50,
                searchAjax: true,
                closeOnSelect: false
            },

        }
    },
    mounted() {
        let app = this;
        app.selectedItem = app.prepareSelected(app.selectedItems);

        if (app.configSelect.mountedSearch) {
            app.searchItems('');
        }
    },
    watch: {
        selectedItem: function(newVal, oldVal) {
            let app = this;
            if (app.configSelect.multiple) {
                app.$emit('update', newVal);
            } else {
                app.$emit('update', newVal.value);
            }
        }
    },
    computed: {
        configSelect: function() {
            let app = this;
            let new_config = app.concatConfig(app.defaultConfig, app.config);
            return new_config;
        },
    },
    methods: {
        mSelectAll (selected) {
            let app = this;
            if (app.selectAll) {
                app.selectedItem = app.sItems;
            } else {
                app.selectedItem = [];
            }
        },
        prepareSelected (selected) {
            let app = this;
            if (app.configSelect.multiple) {
                return Object.keys(selected).length ? selected : [];
            } else {
                let s = app.sItems ? app.sItems.find(e => e.value == selected) : null;
                if (typeof s === 'undefined') {
                    git
                    pull
                    s = null;
                }
                return s;
            }
        },
        selected (key) {
            let app = this;
            if (app.configSelect.multiple) {
                if (Object.keys(this.selectedItem).length) {
                    return this.selectedItem.find(e => e.value == key);
                }
                return false;
            } else {
                return this.selectedItem && this.selectedItem.value == key ? true : false;
            }
        },
        limitText (count) {
            return `and ${count} other items`
        },
        searchItems (query) {
            let app = this;
            if (!app.configSelect.searchAjax) {
                return false;
            }
            app.isLoading = true;
            app.sItems = []
            let params = {
                query: query,
                limit: app.optionsLimit
            }
            if (app.searchData.params) {
                params = {...params, ...app.searchData.params }
            }
            axios.get(app.searchData.url, {params: params}).then(response => {
                app.isLoading = false;
                app.sItems = response.data
            }).catch(function (resp) {
                console.error(resp);
            });
        },
        clearAll () {
            this.selectedItem = []
        },
        concatConfig (a, b) {
            let c = {}, key;
            for (key in a) {
                if (a.hasOwnProperty(key)) {
                    c[key] = key in b ? b[key] : a[key];
                }
            }

            return c;
        }
    }
}
</script>
