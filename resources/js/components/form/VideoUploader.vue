<template>
    <div>
        <div class="uploader"
            @dragenter="OnDragEnter"
            @dragleave="OnDragLeave"
            @dragover.prevent
            @drop="onDrop"
            :class="{ dragging: isDragging }">

            <div class="upload-control" v-show="videos.length && videosLimit > videos.length">
                <label for="file">{{translate('imageupload.selectfile')}}</label>
            </div>

            <div v-show="!videos.length" class="up-wrap">
                <i class="fa fa-cloud-upload"></i>
                <p class="up-descr">{{translate('imageupload.drag_your_videos_here')}}</p>
                <div class="up-subdescr">{{translate('imageupload.unlimited')}}</div>
                <div class="file-input">
                    <label :for="'file' + field">{{translate('imageupload.selectfile')}}</label>
                    <input type="file" :id="'file' + field" @change="onInputChange">
                </div>
            </div>

            <div class="videos-preview" v-show="videos.length && files">
                <div class="img-wrapper" v-for="(video, index) in videos" :key="index" style="position:relative;">
                    <video :src="video" controls style="width:100%;"></video>
                    <b-icon icon="x-circle" font-scale="1.5" variant="danger" class="delete-icon" @click="deleteVideo(index)"></b-icon>
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
    props: ['videos', 'field'],
    data: () => ({
        files: [],
        isDragging: false,
        dragCount: 0,
        videosLimit: 1,
        alerterror: {
            dismissMessage: '',
            showDismissibleAlert: false
        }
    }),
    mounted() {
        let app = this;
        if (app.videos) {
            for (const videoKey in app.videos) {
                let imName = app.videos[videoKey];
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
            Array.from(files).forEach(file => this.addVideo(file));
        },
        onDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            this.isDragging = false;
            const files = e.dataTransfer.files;
            Array.from(files).forEach(file => this.addVideo(file));
        },
        deleteVideo(index) {
            var app = this;
            app.videos.splice(index, 1);
            app.files.splice(index, 1);
        },
        addVideo(file) {
            var app = this;
            if (app.videos.length >= app.videosLimit) {
                app.showAlert('max files ' + app.videosLimit);
                return;
            }

            if (!file.type.match('video.*')) {
                app.$toastr.e(`${file.name} is not an video`);
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
            const reader = new FileReader();
            reader.onload = function(e) {
                app.videos = [e.target.result];
                ready = true;
            };
            reader.readAsDataURL(file);
        },
        upload() {
            let app = this;
            app.$emit('update', app.videos);
        },
        showAlert(message) {
            this.alerterror.showDismissibleAlert = true;
            this.alerterror.dismissMessage = message;
        }
    }
}
</script>

