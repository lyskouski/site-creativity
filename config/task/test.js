/**
 * Run JavaScript tests by using Karma (Jasmine)
 *
 * @since 2016-12-15
 * @author Viachaslau Lyskouski
 */

// Gulp initialization
var gulp = require('gulp');
// Server init
var server = require('karma').Server;

// Gulp task 'test' run
gulp.task('test', function (callback) {
    new server({
        configFile: __dirname + '/../../tests/public/js/karma.conf.js',
        // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
        browsers: ['PhantomJS'],
        // Continuous Integration mode (Karma captures browsers, runs the tests and exits)
        singleRun: true
    }, callback).start();
});