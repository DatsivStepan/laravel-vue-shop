<template>
    <div v-if="gridData">
            <table-filter :filters="gridData.filters" :bulk="bulk" :bulk_actions="gridData.bulkActions"
                      @filter-change="filterChange" @bulk-change="bulkActionChange" ref="tSearch"></table-filter>

        <div class="all-sales-table">
                <div class="table-overflow" :class="{'custom-padding' : this.status}">
                <table :class="options.table.class" class="custom-grid-table ">
                    <thead class="table-header">
                    <tr>
                        <th v-if="options.enableDraggble" class="th-first">
                            <div class="table-draggablev header-drag">
                                <b-icon icon="arrows-move" class='page__grab-icon'></b-icon>
                            </div>
                        </th>
                        <th v-if="typeof gridData.bulkActions !== 'undefined'" class="th-input" :class="isActive">
                            <input class="table-input" type="checkbox" id="td-input-title" v-model='bulk.checkAll' @change="bulkCheckAll()">
                            <label for="td-input-title"></label>
                        </th>

                        <th v-for="(headerItem, column) in gridData.headerItems" :key="column">
                            <a class="table-link" href="#" v-if="(typeof headerItem.sort !== 'undefined') && headerItem.sort" @click="sortBy(column)">
                                <b-icon v-if="(sortColumn == column) && (sortReverse == false)" icon="arrow-down"
                                        font-scale="1"></b-icon>
                                <b-icon v-if="(sortColumn == column) && (sortReverse == true)" icon="arrow-up"
                                        font-scale="1"></b-icon>
                                {{ headerItem.value }}
                            </a>
                            <span class='table-span' v-else >{{ (typeof headerItem.value !== 'undefined') ? headerItem.value : headerItem }}</span>
                        </th>
                    </tr>
                    </thead>

                    <draggable v-if="gridData.rowsItems.length" :list="gridData.rowsItems"
                            :options="{handle:'.page__grab-icon'}" tag="tbody" @end="updateItemOrder"
                            :disabled="!options.enableDraggble">
                        <tr v-for="(rowItem, index) in gridData.rowsItems" :key="index"
                            :class="{ 'not-active': !((typeof rowItem.activeRow !== 'undefined') ? rowItem.activeRow : true)}">
                                    <td v-if="options.enableDraggble"  class="table-draggable h5 mb-2">
                                        <b-icon icon="list" class='page__grab-icon'></b-icon>
                                    </td>
                                    <td v-if="typeof gridData.bulkActions !== 'undefined'" class="td-input" :class="isActive">
                                        <input class="table-input" :id="'td-input'+ index" type="checkbox" :value="rowItem.index" v-model="bulk.selected"
                                            @change="$refs.tSearch.updateBulk(bulk)">
                                        <label :for="'td-input'+ index"></label>
                                    </td>

                            <td v-for="(item, index) in rowItem" :key="index" v-if="checkShowItemData(index, item.hidden)">
                                <router-link v-if="item.type=='link'" :title="rowItem.value"
                                            :to="{name: item.params.routeName, params: item.params.routeParams}">
                                    {{ item.value }}
                                </router-link>
                                <span v-if="item.type=='action'" class="table-row-icon cursor-pointer"
                                    :title="item.value.params.title" @click="callMethod(item.value.params)">
                                    <b-icon :icon="item.value.params.icon"></b-icon>
                                </span>

                                <toggle-button v-if="item.type=='switch'" v-model="item.value" :sync="true"
                                            @change="callMethod(item.options.params)"/>

                                <span v-html="convertData(item)"></span>
                            </td>

                            <td v-if='rowItem.actions' class="action-dropdown" :class="activeAction">
                                <actions-dropdown @dropdown-show='change' @dropdown-hide='hideDropdown' :dimension="'horizontal'">
                                    <li v-for="(action, index) in rowItem.actions" :key="index">
                                        <router-link v-if="action.type == 0" :title="action.title"
                                                    :to="{name: action.params.route, params: action.params.params}"> <!-- 0 - go to route -->
                                            {{ action.title }}
                                        </router-link>
                                        <a v-else-if="action.type == 1" @click="callMethod(action.params)"> <!-- 1 - call vue method -->
                                            {{action.title}}
                                        </a>
                                    </li>
                                </actions-dropdown>
                            </td>
                        </tr>
                    </draggable>

                    <tbody v-else>
                        <tr >
                            <td :colspan="tableColspan" class="text-center">
                                {{ translate('system.no_data') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">
                <table-pagination-size class="footer-form-control" :meta="data.meta"
                                    @change-pagination-size="changePaginationSize" :limit="2"></table-pagination-size>
                <pagination
                    :data="data"
                    @pagination-change-page="getModels"
                    :limit="1"
                    align="center"
                >
                </pagination>
            </div>
        </div>
    </div>
</template>

<script>
import TableFilter from './TableFilter.vue';
import TablePaginationSize from './TablePaginationSize.vue';
import ActionsDropdown from './ActionsDropdown.vue';
import Draggable from 'vuedraggable';
import '@sass/component/table.scss';
import {ToggleButton} from 'vue-js-toggle-button';
import * as qs from "qs";

export default {
    components: {
        TableFilter,
        TablePaginationSize,
        ActionsDropdown,
        Draggable,
        ToggleButton
    },
    props: {
        gridData: {
            type: Object,
        },
        data: {
            data: Object,
        },
        baseUrl: {
            type: String
        },
        isAll: {
            type: Boolean
        }
    },
    data: function () {
        return {
            page: 1,
            sortColumn: '',
            sortReverse: false,
            tableFilters: {},
            bulk: {
                action: '',
                checkAll: false,
                selected: [],
            },
            status: null,
            isAct: false
        }
    },
    computed: {
        perPage: {
            get: function () {
                let app = this;
                return app.data.meta.per_page
            },
            set: function (newValue) {
                let app = this;
                app.data.meta.per_page = newValue;
            }
        },
        options: function () {
            let app = this;
            let options = app.gridData.options;
            let enableDraggble = false;

            if (options) {
                enableDraggble = ('enableDraggble' in options) ? options.enableDraggble : false;
            }

            return {
                table: {
                    class: 'table table-hover'
                },
                enableDraggble: enableDraggble && (app.getSettingValue('enable_table_sort_order') == 1)
            }
        },
        tableColspan: function () {
            let app = this;
            return Object.keys(app.gridData.headerItems).length
                + (typeof app.gridData.bulkActions !== 'undefined' ? 1 : 0) +
                (app.options.enableDraggble ? 1 : 0);
        },
        isActive(){
            return {
                'active-td' : this.isAct,
            }
        },
        activeAction(){
            return {
                'active-action' : this.isAct
            }

        }
    },

    mounted() {
         this.toggleColumn()
    },
    methods: {
        toggleColumn(){


            if(this.isAll){
                this.isAct = !this.isAct
            }
        },
        updateItemOrder: function () {
            let app = this
            let items = app.gridData.rowsItems.map(function (item, index) {
                return item.index;
            })

            axios.post(app.baseUrl + '/reload-sort-order', {items: items})
                .then(function (resp) {
                    console.log(resp)
                })
                .catch(function (resp) {
                    console.error('error delete change status');
                });
        },
        getModels(page) {
            let app = this
            let sortValue = '';
            if (app.sortColumn) {
                sortValue  = (app.sortReverse ? '-' : '') + app.sortColumn;
            }

            if (typeof page !== 'undefined') {
                app.page = page;
            }

            app.$emit('get-models', app.page, sortValue, app.tableFilters, app.perPage)
        },
        checkShowItemData(index, hidden = null) {
            return (index !== 'actions') && (index !== 'index') && (index != 'activeRow') && (hidden !== true);
        },
        convertData(item) {
            let app = this;
            let res = item.value ? item.value : '-';
            if (typeof item.type !== "undefined") {
                switch (item.type) {
                    case 'datetime':
                        res = item.value ? app.$moment(item.value).format(app.translate('langconfig.vue_date_format.admin_datetime')) : '-'
                        break;
                    case 'date':
                        res = item.value ? app.$moment(item.value).format(app.translate('langconfig.vue_date_format.admin_date')) : '-';
                        break;
                    case 'link':
                    case 'action':
                    case 'activeRow':
                    case 'switch':
                        res = '';
                        break;
                    default:
                        return item.value ? item.value : '-';
                }
            }
            return res;
        },
        changePaginationSize(size) {
            let app = this;
            app.page = 1;
            app.perPage = size;

            app.getModels();
        },

        // bulk actions
        bulkCheckAll() {
            let app = this
            let rowsItems = app.gridData.rowsItems;
            app.bulk.selected = [];
            if (app.bulk.checkAll) {
                for (let rowItem in rowsItems) {
                    app.bulk.selected.push(rowsItems[rowItem].index);
                }
                app.$refs.tSearch.updateBulk(app.bulk);
            }
        },
        bulkActionChange(action) {
            let app = this;
            this.$confirm(app.translate('system.confirm.question.are_you_sour'), '', '').then(() => {
                let url, params, method;

                if (app.gridData.bulkActions.details) {
                    url =  app.gridData.bulkActions.details[action].url;
                    let idsKey = app.gridData.bulkActions.details[action].ids ? app.gridData.bulkActions.details[action].ids : 'ids';

                    params = {};
                    if (app.gridData.bulkActions.details[action].params) {
                        params = Object.assign({}, app.gridData.bulkActions.details[action].params)
                    }
                    params[idsKey] = app.bulk.selected;

                    method = app.gridData.bulkActions.details[action].method ? app.gridData.bulkActions.details[action].method : 'post'
                } else {
                    url = `${app.gridData.bulkActions.url}/${action}`;
                    params = {ids: app.bulk.selected}
                    method = 'post';
                }

                axios({
                    method:method,
                    url: url,
                    data: params,
                    config: { paramsSerializer: params => { return qs.stringify(params)}}
                }).then(function (resp) {
                    app.page = 1;
                    app.getModels();
                    app.bulk.selected = [];
                    app.bulk.action = '';
                    app.bulk.checkAll = false;

                    app.$refs.tSearch.updateBulk(app.bulk);
                })
                    .catch(function (resp) {
                        console.error('error approve');
                    });
            });
        },
        // bulk actions

        filterChange(filters) {
            let app = this;
            app.page = 1;
            app.tableFilters = filters;

            app.getModels();
        },
        sortBy(column) {
            let app = this;
            app.sortReverse = (app.sortColumn == column) ? !app.sortReverse : false;
            app.sortColumn = column;
            app.page = 1;

            app.getModels();
        },
        changeStatus(url, id, value) {
            let app = this;
            this.$confirm(app.translate('system.confirm.question.you_want_change_status'), '', '').then(() => {
                axios.post(url, {id: id, value: value}).then(function (resp) {
                    console.log(resp)
                })
                    .catch(function (resp) {
                        console.error('error delete change status');
                    });
            });
        },
        deleteEntry(params) {
            let app = this;
            let url = params['url'];
            url += params['id'] ? params['id'] : '';

            let confirmOption = {
                cancelButtonText: app.translate('system.confirm.no'),
                confirmButtonText: app.translate('system.confirm.yes'),
            }
            this.$confirm(app.translate('system.confirm.question.you_want_delete'), app.translate('system.delete'), '', confirmOption).then(() => {
                axios.delete(url).then(function (resp) {
                    app.getModels();
                })
                    .catch(function (resp) {
                        console.error('error delete');
                    });
            });
        },
        callMethod(action) {
            this[action.methodName](action.params);
        },
        actionApi(params) {
            let app = this;
            axios[params.method](params.route, params.routeParams).then(function (resp) {
                app.getModels();
            })
                .catch(function (resp) {
                    console.error('error call route ' + params.route);
                });
        },
        emit(params) {
            this.$emit(params['method'], params);
        },

        change(value){
            if(this.gridData.rowsItems.length === 1){
                this.status = value
            }
        },
        hideDropdown(value){
            if(this.gridData.rowsItems.length === 1){
                this.status = value
            }
        }
    }
}
</script>
