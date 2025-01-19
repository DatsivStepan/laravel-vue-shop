<template>
  <div id="example">
    <div class="container">
      <div id="bar">
        <h1>Vue Email Editor (Demo)</h1>

        <button v-on:click="saveDesign">Save Design</button>
        <button v-on:click="exportHtml">Export HTML</button>
      </div>

      <EmailEditor ref="emailEditor" v-on:load="editorLoaded" v-bind:tools="tools" />
    </div>
  </div>
</template>

<script>
import { EmailEditor } from 'vue-email-editor';
import sample from './sample.json';
export default {
    name: 'example',
    components: {
        EmailEditor
    },
    data: function () {
        return {
            tools: {
                text: {
                    enabled: false
                },
                'custom#mytool': {
                    name: 'my_tool',
                    label: 'My Tool',
                    icon: 'fa-smile',
                    supportedDisplayModes: ['web', 'email'],
                    options: {
                      colors: { // Property Group
                        title: "Colors", // Title for Property Group
                        position: 1, // Position of Property Group
                        options: {
                          "textColor": { // Property: textColor
                            "label": "Text Color", // Label for Property
                            "defaultValue": "#FF0000",
                            "widget": "color_picker" // Property Editor Widget: color_picker
                          },
                          "backgroundColor": { // Property: backgroundColor
                            "label": "Background Color", // Label for Property
                            "defaultValue": "#FF0000",
                            "widget": "color_picker" // Property Editor Widget: color_picker
                          }
                        }
                      }
                    },
                    values: {},
                    renderer: {
                      Viewer: unlayer.createViewer({
                        render(values) {
                          return `<div style="color: ${values.textColor}; background-color: ${values.backgroundColor};">I am a custom tool.</div>`
                        }
                      }),
                      exporters: {
                        web: function(values) {
                          return `<div style="color: ${values.textColor}; background-color: ${values.backgroundColor};">I am a custom tool.</div>`
                        },
                        email: function(values) {
                          return `<div style="color: ${values.textColor}; background-color: ${values.backgroundColor};">I am a custom tool.</div>`
                        }
                      },
                      head: {
                        css: function(values) {},
                        js: function(values) {}
                      }
                    }
                  }
            },
        }
    },
    methods: {
        editorLoaded() {
            this.$refs.emailEditor.editor.loadDesign(sample);
        },
        saveDesign() {
            this.$refs.emailEditor.editor.saveDesign(
              (design) => {
                console.log('saveDesign', JSON.parse(design));
              }
            )
        },
        exportHtml() {
            this.$refs.emailEditor.editor.exportHtml(
              (data) => {
                console.log('exportHtml', data);
              }
            )
        }
    }
}

</script>

<style>
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
}
#app, #example {
  height: 100vh;
}
#example .container {
  display: flex;
  flex-direction: column;
  position: relative;
  height: 100%;
}
#bar {
  flex: 1;
  background-color: #40B883;
  color: #FFF;
  padding: 10px;
  display: flex;
  max-height: 40px;
}
#bar h1 {
  flex: 1;
  font-size: 16px;
  text-align: left;
}
#bar button {
  flex: 1;
  padding: 10px;
  margin-left: 10px;
  font-size: 14px;
  font-weight: bold;
  background-color: #000;
  color: #FFF;
  border: 0px;
  max-width: 150px;
  cursor: pointer;
}
</style>