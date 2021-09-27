const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.copy('resources/fonts/*', 'public/fonts/');

mix.postCss('resources/css/app.css', 'public/css').options({processCssUrls: false});
mix.postCss('resources/css/hyper-app.css', 'public/css').options({processCssUrls: false});
mix.postCss('resources/css/icons.css', 'public/css').options({processCssUrls: false});
mix.postCss('resources/css/vendor/dataTables.bootstrap4.css', 'public/css/vendor').options({processCssUrls: false});
mix.postCss('resources/css/vendor/responsive.bootstrap4.css', 'public/css/vendor').options({processCssUrls: false});


mix.js('resources/js/app.js', 'public/js/');
mix.js('resources/js/pages/*', 'public/js/pages/');
mix.js('resources/js/hyper.js', 'public/js/');

mix.copy('resources/js/vendor.js', 'public/js/');
mix.copy('resources/js/vendor/jquery.dataTables.min.js', 'public/js/vendor/');
mix.copy('resources/js/vendor/dataTables.bootstrap4.js', 'public/js/vendor/');
mix.copy('resources/js/vendor/dataTables.responsive.min.js', 'public/js/vendor/');
mix.copy('resources/js/vendor/responsive.bootstrap4.min.js', 'public/js/vendor/');
mix.copy('resources/js/vendor/dataTables.checkboxes.min.js', 'public/js/vendor/');
mix.copy('resources/js/vendor/Chart.bundle.min.js', 'public/js/vendor/');
mix.copy('resources/js/vendor/apexcharts.min.js', 'public/js/vendor/');

