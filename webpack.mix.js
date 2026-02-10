const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js').vue()
    .js('resources/js/galeria.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps();

if (mix.inProduction()) {
    mix.version();
}
