<template>
    <div>
        <div class="uploader"
            @dragenter="OnDragEnter"
            @dragleave="OnDragLeave"
            @dragover.prevent
            @drop="onDrop"
            :class="{ dragging: isDragging }">

            <div class="upload-control" v-show="images.length && imagesLimit > images.length">
                <label for="file">{{translate('imageupload.selectfile')}}</label>
            </div>

            <div v-show="!images.length" class="up-wrap">
                <i class="fa fa-cloud-upload"></i>
                <p class="up-descr">{{translate('imageupload.dragyourimageshere')}}</p>
                <div class="up-subdescr">{{translate('imageupload.files_supported')}}</div>
                <div class="file-input">
                    <label :for="'file' + field">{{translate('imageupload.selectfile')}}</label>
                    <input type="file" :id="'file' + field" @change="onInputChange">
                </div>
            </div>

            <div class="images-preview" v-show="images.length && files">
                <div class="img-wrapper" v-for="(image, index) in images" :key="index" style="position:relative;">
                    <img :src="image" :alt="`Image Uplaoder ${index}`">
                    <b-icon icon="x-circle" font-scale="1.5" variant="danger" class="delete-icon" @click="deleteImage(index)"></b-icon>
                </div>
            </div>

        </div>
        <b-alert v-model="alerterror.showDismissibleAlert" variant="danger" dismissible>
            {{alerterror.dismissMessage}}
        </b-alert>
    </div>
</template>

<script>
import '@sass/component/image-uploader.scss';
export default {
    props: ['images', 'field'],
    data: () => ({
        files: [],
        isDragging: false,
        dragCount: 0,
        imagesLimit: 1,
        alerterror: {
            dismissMessage: '',
            showDismissibleAlert: false
        }
    }),
    mounted() {
        let app = this;
        if (app.images) {
            for (const imageKey in app.images) {
                let imName = app.images[imageKey];
                app.files.push({name: imName, size: 1});
            }
        }
    },
    updated() {
        this.upload();
    },
    methods: {
        OnDragEnter(e) {
            e.preventDefault();

            this.dragCount++;
            this.isDragging = true;
            return false;
        },
        OnDragLeave(e) {
            e.preventDefault();
            this.dragCount--;
            if (this.dragCount <= 0)
                this.isDragging = false;
        },
        onInputChange(e) {
            const files = e.target.files;
            Array.from(files).forEach(file => this.addImage(file));
        },
        onDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            this.isDragging = false;
            const files = e.dataTransfer.files;
            Array.from(files).forEach(file => this.addImage(file));
        },
        deleteImage(index) {
            var app = this;
            app.images.splice(index, 1);
            app.files.splice(index, 1);
        },
        addImage(file) {
            var app = this;
            if (app.images.length >= app.imagesLimit) {
                app.showAlert('max files ' + app.imagesLimit);
                return;
            }
            if (!file.type.match('image.*')) {
                app.$toastr.e(`${file.name} is not an image`);
                return;
            }

            var ready = false;
            var check = function() {
                if (ready === true) {
                    app.upload();
                    return;
                }
                setTimeout(check, 1000);
            }

            check();

            this.files.push(file);
            const img = new Image(),
                reader = new FileReader();
            reader.onload = function(e) {
                app.images = [e.target.result];
                ready = true;
            };
            reader.readAsDataURL(file);
        },
        upload() {
            let app = this;
            app.$emit('changeImage', {files: app.images, field: app.field});
        },
        showAlert(message) {
            this.alerterror.showDismissibleAlert = true;
            this.alerterror.dismissMessage = message;
        }
    }
}
</script>

