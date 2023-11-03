/**
 * Build project
 *
 * @author Viachaslau Lyskouski
 */

// Gulp initialization
var gulp = require('gulp');
var change = require('gulp-change');
var runSequence = require('run-sequence');

gulp.task('build', function (callback) {
    var sources = require('./params/minify.json');

    // Update version and timestamp
    console.log('* updating timestamp');
    var jsonPath = './config/task/params/';
    gulp.src(jsonPath + 'minify.json')
        .pipe(change(function () {
            for (var i in sources) {
                sources[i].datetime = (new Date).getTime();
            }
            return JSON.stringify(sources, {}, "    ");
        }))
        .pipe(gulp.dest(jsonPath));

    // Update version for minified file
    var jsPath = './public/js/';
    gulp.src(jsPath + 'min.js')
        .pipe(change(function (content) {
            var a = content.split("'");
            console.log('* change min version: ', a[3], ' up to ',  sources.default.version);
            return content.split(a[3]).join(sources.default.version);
        }))
        .pipe(gulp.dest(jsPath));

    // Build release (@note: `css`, `js` cannot be triggered in parallel)
    return runSequence(
        'sass',
        'css',
        'js',
        'image',
        callback
    );
});