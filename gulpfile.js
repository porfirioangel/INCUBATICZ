var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.less('app.less');
    mix.copy('bower_components/bootstrap/dist/fonts', 'public/assets/fonts');
   	mix.copy('bower_components/font-awesome/fonts', 'public/assets/fonts');
   	mix.styles([
        'bower_components/bootstrap/dist/css/bootstrap.css',
        'bower_components/fontawesome/css/font-awesome.css',
        'resources/css/sb-admin-2.css',
        'resources/css/timeline.css',
        'resources/css/bootstrap-datepicker.css'
    ], 'public/assets/stylesheets/styles.css', './');
    mix.scripts([
        'bower_components/jquery/dist/jquery.js',
        'bower_components/bootstrap/dist/js/bootstrap.js',
        'bower_components/Chart.js/Chart.js',
        'bower_components/metisMenu/dist/metisMenu.js',
        'resources/js/sb-admin-2.js',
        'resources/js/frontend.js',
        'resources/js/bootstrap-datepicker.js',
        'resources/js/word_counter.js',
        'resources/js/integrantes_generator.js',
        'resources/js/submit_proyecto_form.js',
        'resources/js/submit_login_form.js',
        'resources/js/proponentes_excel.js',
        'resources/js/pdfmake.min.js',
        'resources/js/vfs_fonts.js',
        'resources/js/abstract_proyecto_pdf.js',
    ], 'public/assets/scripts/frontend.js', './');
});


