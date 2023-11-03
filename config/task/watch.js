/**
 * Changes observer
 *
 * @author Viachaslau Lyskouski
 * @since 2016-07-11
 */

// Gulp initialization
var gulp = require('gulp');

// Task for automated changes observer
gulp.task('watch', function () {
    var dir = 'public/';

    gulp.watch(dir + '/css/**/*', function () {
        return gulp.run('css');
    });

    gulp.watch(dir + '/js/**/*', function () {
        return gulp.run('js');
    });

    gulp.watch(dir + '/img/**/*', function () {
        return gulp.run('image');
    });

    gulp.watch(dir + '/sass/**/*', function () {
        return gulp.run('sass');
    });
});