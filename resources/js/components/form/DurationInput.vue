<template>
    <div class="setting-duration">
        <input
            type="number"
            min="0"
            v-model="inputValue"
            class="form-control setting-dropdown"
            @change="changeValue()"
        >

        <input type="hidden" v-model="selectValue">
        <v-select
            id="enrol_end"
            class="style-chooser"
            v-model="selectedVAl"
            placeholder="Select duration"
            :options="selectOptions"
            value="value"
            @input="select"
            label="text">
            <template v-slot:option="option">
                {{ option.text }}
            </template>
        </v-select>
    </div>

</template>
<script>
import '@sass/component/settings.scss'

export default {
    props: {
        model: {
            type: Number,
        }
    },
    data: function () {
        let app = this;
        return {
            inputValue: 0,
            selectValue: '604800',
            selectOptions: [
                {text: app.translate('system.duration_picker.week'), value: 604800},
                {text: app.translate('system.duration_picker.day'), value: 86400},
                {text: app.translate('system.duration_picker.hour'), value: 3600},
                {text: app.translate('system.duration_picker.minute'), value: 60},
            ],
            selectedVAl: null
        }
    },
    mounted() {
        let app = this;
        app.prepareModelValue();
    },
    methods: {
        select(val){
            this.selectValue = val.value
        },
        prepareModelValue() {
            let app = this;
            let value = app.model;
            if (value) {
                if ((value >= 604800) && (value % 604800 == 0)) {
                    app.inputValue = value / 604800;
                    app.selectValue = 604800;
                    app.selectedVAl = app.translate('system.duration_picker.week')
                } else if ((value >= 86400) && (value % 86400 == 0)) {
                    app.inputValue = value / 86400;
                    app.selectValue = 86400;
                    app.selectedVAl = app.translate('system.duration_picker.day')
                } else if ((value >= 3600) && (value % 3600 == 0)) {
                    app.inputValue = value / 3600;
                    app.selectValue = 3600;
                    app.selectedVAl = app.translate('system.duration_picker.hour')
                } else if ((value >= 60) && (value % 60 == 0)) {
                    app.inputValue = value / 60;
                    app.selectValue = 60;
                    app.selectedVAl = app.translate('system.duration_picker.minute')
                }
            }
        },
        changeValue() {
            let app = this;
            app.$emit('update', app.inputValue * app.selectValue);
        }
    }
}
</script>
