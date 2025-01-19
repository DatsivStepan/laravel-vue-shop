const mix = require('laravel-mix');
const path = require('path');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.setPublicPath(process.env.MIX_PUBLIC_PATH);
mix.js('resources/js/app.js', process.env.MIX_PUBLIC_PATH + '/js')
    .sass('resources/sass/app.scss', process.env.MIX_PUBLIC_PATH + '/css');
mix.webpackConfig({
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js/"),
            "@component": path.resolve(__dirname, "resources/js/components"),
            "@page": path.resolve(__dirname, "resources/js/pages"),
            "@themes": path.resolve(__dirname, "resources/js/themes"),
            "@modules": path.resolve(__dirname, "Modules/"),
            "@sass": path.resolve(__dirname, "resources/sass"),
        }
    }
});
