<template>
    <li :class="{'nav-item': !route.hasOwnProperty('child'), 'main-accordion-folder': route.hasOwnProperty('child'), 'accordion-without-icon': !route.icon && route.hasOwnProperty('child')}"
        v-if="Boolean(route.show)">
        <span v-if="route.hasOwnProperty('child')" class="item-accordion collapsed"  @click="watchHeight"

        >
            <ion-icon v-if="route.icon" :name="route.icon" class="item-icon"></ion-icon>
            <span class="text" :title="route['name']" >
                            {{route['name']}}
            </span>
            <ion-icon name="chevron-forward" class="close-accordion item-arrow"></ion-icon>
            <ion-icon name="chevron-down"  class="open-accordion item-arrow"></ion-icon>
        </span>

        <div v-if="route.hasOwnProperty('child')"   :id="'collapse-' + rkey" :accordion="'my-accordion' + level" @shown="showCollapce(route)" class="accordion-folder">
            <left-menu-item  v-for="(routeChild, key) in route.child" :key="key" :level="level + 1" :rkey="rkey + '-' + key" :route="routeChild"></left-menu-item>
        </div>

        <a href="#" v-if="!route.hasOwnProperty('child')" class="nav-link"
            @click="handleClick($event,{ name : route.path.name, params: route.path.hasOwnProperty('params') ? route.path.params : {} })" :key="rkey">
                <ion-icon v-if="route.icon" :name="route.icon"></ion-icon>
                <span class="text" :title="route.name">
                  {{route.name}}
                </span>
        </a>
    </li>
</template>

<script>
import LeftMenuItem from '@component/LeftMenuItem.vue';
export default {
    name: 'LeftMenuItem',
    components: {
        LeftMenuItem
    },
    props: {
        level: {
            type: Number,
            default: 0
        },
        rkey: {
            type: [String, Number],
        },
        rindex: {
            type: Number,
        },
        route: {
            type: Object,
        }
    },
    data() {
        let app = this;
        return {}
    },
    mounted() {
        document.querySelector('body').addEventListener('click', function (){
            document.querySelectorAll('.left-menu.close-main-menu .menu > .main-accordion-folder').forEach(el => {
                el.classList.remove('active')
            })
            document.querySelectorAll('.left-menu.close-main-menu .menu >.main-accordion-folder > .accordion-folder').forEach(el => {
                el.classList.remove('show')
            })
            document.querySelectorAll('.left-menu.close-main-menu .menu > .main-accordion-folder > .item-accordion').forEach(el => {
                el.classList.remove('not-collapsed')
                el.classList.add('collapsed')
            })
        })
    },
    methods: {
        handleClick (e,el) {
            e.preventDefault()
            document.querySelectorAll('.left-menu-block .nav-item').forEach(el => {
                el.classList.remove('router-link-exact-active')
            })
            this.$el.classList.add('router-link-exact-active')
           document.querySelectorAll('.accordion-folder').forEach(el => {
               el.classList.remove('show')
           })
            document.querySelectorAll('.main-accordion-folder').forEach(el => {
                el.classList.remove('active')
            })
            document.querySelectorAll('.item-accordion').forEach(el => {
                el.classList.remove('not-collapsed')
                el.classList.add('collapsed')
            })
            this.$root.$emit('bv::toggle:close')
            this.$router.push(el)
        },
        watchHeight(e){
            e.stopPropagation()
            if(this.$el.parentElement.classList[0] === 'clearfix'){
                document.querySelectorAll('.left-menu.close-main-menu .menu > .main-accordion-folder').forEach(el => {
                    el.classList.remove('active')
                })
                document.querySelectorAll('.left-menu.close-main-menu .menu >.main-accordion-folder > .accordion-folder').forEach(el => {
                    el.classList.remove('show')
                })
                document.querySelectorAll('.left-menu.close-main-menu .menu > .main-accordion-folder > .item-accordion').forEach(el => {
                    el.classList.remove('not-collapsed')
                    el.classList.add('collapsed')
                })
            }
           this.$el.classList.toggle('active')
            console.log(this.$el.parentElement.classList[0])
            this.$el.childNodes[0].nextElementSibling.classList.toggle('show')
            let parentAccordionFolder = document.querySelectorAll('.left-menu.close-main-menu .menu > .main-accordion-folder > .accordion-folder ');
           setTimeout(val => {
               parentAccordionFolder.forEach(el => {
                   let heightNode = el.offsetHeight;
                   if(this.$el.lastElementChild === el){
                       if(heightNode > 400){
                           el.classList.add('active-fix')
                       }
                   }
               })
            },500)
        },
        showCollapce(route) {
            let app = this;
            if (typeof route.path !== "undefined") {
                app.$router.push({ name: route.path.name, params: route.path.hasOwnProperty('params') ? route.path.params : {} });
            }
        }
    }
}
</script>
