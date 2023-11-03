/**
 * Minimze images
 *
 * @author Viachaslau Lyskouski
 */

// Gulp initialization
var gulp = require('gulp');
var del = require('del');
var imagemin = require('gulp-imagemin');

var dir = 'public/img';

// Minify images
gulp.task('image', ['cleanup-image'], function () {
    return gulp.src(dir + '/**/*')
        .pipe(imagemin())
        .pipe(gulp.dest(dir + '.min'));
});

// Clear directory
gulp.task('cleanup-image', function () {
    console.log('[!] Cleanup minified image\' folder');
    return del([dir + '.min/**/*']);
});