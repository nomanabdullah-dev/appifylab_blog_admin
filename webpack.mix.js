const mix = require('laravel-mix');


mix.js('resources/js/app.js', 'public/js').vue({ version: 2 })
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
mix.styles(['public/css/main.css',
    'public/css/grid.min.css'
    ], 'public/css/app.css');
