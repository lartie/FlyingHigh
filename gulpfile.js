var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.copy('resources/assets/css', 'public/css');
    mix.copy('resources/assets/font', 'public/font');
    mix.copy('resources/assets/js', 'public/js');
    mix.copy('resources/assets/img', 'public/img');
});
