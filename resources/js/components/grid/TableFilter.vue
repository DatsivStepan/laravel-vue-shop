<template>
    <div class="grid-filters-block">
        <div class="filter-block" :class="{'no-right':!bulk.selected.length}">
            <div
                v-for="(filter, index) in filters"
                :key="index"
                :class="filter.options.className + ' m-element'"

            >

                <b-input-group v-if="filter.type == 'text' && index == 'search'" >
                    <b-input-group-append is-text class="table-icon">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 16 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M15.7548 14.394L12.1277 10.7669C13.0009 9.60434 13.4723 8.18932 13.4707 6.73536C13.4707 3.02151 10.4492 0 6.73536 0C3.02151 0 0 3.02151 0 6.73536C0 10.4492 3.02151 13.4707 6.73536 13.4707C8.18932 13.4723 9.60434 13.0009 10.7669 12.1277L14.394 15.7548C14.5776 15.9189 14.8171 16.0065 15.0632 15.9996C15.3094 15.9927 15.5436 15.8919 15.7177 15.7177C15.8919 15.5436 15.9927 15.3094 15.9996 15.0632C16.0065 14.8171 15.9189 14.5776 15.7548 14.394ZM1.92439 6.73536C1.92439 5.78384 2.20655 4.85369 2.73518 4.06253C3.26382 3.27137 4.01519 2.65473 4.89428 2.2906C5.77337 1.92647 6.7407 1.8312 7.67393 2.01683C8.60717 2.20246 9.4644 2.66066 10.1372 3.33349C10.8101 4.00632 11.2683 4.86355 11.4539 5.79679C11.6395 6.73002 11.5442 7.69735 11.1801 8.57644C10.816 9.45553 10.1994 10.2069 9.40819 10.7355C8.61703 11.2642 7.68688 11.5463 6.73536 11.5463C5.45988 11.5448 4.23708 11.0374 3.33518 10.1355C2.43328 9.23364 1.92592 8.01084 1.92439 6.73536Z"
                                fill="#C4C4C4"
                            />
                        </svg>
                    </b-input-group-append>
                    <b-form-input
                        type="search"
                        class="table-search"
                        v-model="filtersValues[index]"
                        :name="index"
                        :placeholder="filter.options.placeholder"
                        @keyup="changeFilter()"
                    >
                    </b-form-input>
                </b-input-group>
                <date-picker
                    class="table-datepicker"
                    v-if="filter.type == 'date_range'"
                    v-model="filtersValues[index]"
                    :placeholder="filter.options.placeholder"
                    @change="changeFilter()"
                    range
                ></date-picker>
                <input
                    v-if="filter.type == 'text' && index != 'search'"
                    v-model="filtersValues[index]"
                    type="text"
                    class="form-control"
                    @keyup="changeFilter()"
                    :name="index"
                    :id="'filter' + index"/>

                <div class="table-status"  v-if="filter.type == 'select'">
                    <div class="for-icon">
                        <div class="icon-holder">
                            <ion-icon name="chevron-down"></ion-icon>
                        </div>
                        <select
                            v-if="filter.type == 'select'"
                            v-model="filtersValues[index]"
                            class="form-control table-control-status"
                            :id="'filter' + index"
                            @focus="activeIcon = true"
                            @blur="activeIcon = false"
                            @change="changeFilter()">
                            <option :value="null" hidden v-if="(typeof filter.options.placeholder !== 'undefined') && filter.options.placeholder">
                                {{ filter.options.placeholder }}
                            </option>

                            <option value="" v-if="typeof filter.options.options[''] !== 'undefined'">
                                {{ filter.options.options[""] }}
                            </option>
                            <option
                                v-for="(option, index) in filter.options.options"
                                :key="index"
                                v-if="index !== ''"
                                :value="index"
                            >
                                {{ option }}
                            </option>
                        </select>
                    </div>
                </div>


            </div>
        </div>

        <div class="right-action" v-if="bulk.selected.length">
            <div v-if="bulk.selected.length" class="row">
                <div class="col-sm-12">
                    <div class="right-action-wrapper">
                        <div class="for-icon" :class="{active:activeIcon}">
                            <div class="icon-holder">
                                <ion-icon name="chevron-down"></ion-icon>
                            </div>
                            <select
                                class="form-control table-control table-control-first"
                                v-model="bulk.action"
                                @change="bulkActionChange()"
                                :disabled="bulk.disable">
                                <option hidden value="">{{ translate("system.selectaction") }}</option>
                                <option v-for="(action, index) in bulk_actions.actions" :value="index">
                                    {{ action }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import "@sass/component/table-search.scss";
export default {
    components: {
        DatePicker
    },
    props: {
        filters: {
            type: Object
        },
        bulk: {
            type: Object
        },
        bulk_actions: {
            type: Object
        }
    },
    data: function() {
        return {
            filtersValues: {},
            activeIcon: false
        };
    },
    methods: {
        updateBulk(bulk) {
            this.bulk = bulk;
        },
        bulkActionChange() {
            let app = this;
            app.$emit("bulk-change", app.bulk.action);
        },
        changeFilter() {
            let app = this;
            let filter = {};
            for (const fil in app.filters) {
                filter[fil] = app.prerareResValue(
                    app.filters[fil].type,
                    app.filtersValues[fil]
                );
            }
            app.$emit("filter-change", filter);
        },
        prerareResValue(type, value) {
            let app = this;
            if (type == "date_range" && value) {
                if (value[0] && value[1]) {
                    return (
                        app.$moment(value[0])
                            .format(
                                app.translate(
                                    "langconfig.vue_date_format.range_filter"
                                )
                            ) +
                        "," +
                        app.$moment(value[1])
                            .format(
                                app.translate(
                                    "langconfig.vue_date_format.range_filter"
                                )
                            )
                    );
                }
            } else {
                return value;
            }
        }
    },
    mounted() {
        let app = this;
        if (app.filters) {
            let temp = {};
            for (const key in app.filters) {
                let filter = app.filters[key];

                // let val = filter.options.value !== null ? filter.options.value : "";
                let val = filter.options.value;

                temp[key] = val;
            }
            app.filtersValues = temp;
        }
    }
};
</script>
