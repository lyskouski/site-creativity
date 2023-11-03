/**
 * SASS compilation to CSS files
 *
 * @author Viachaslau Lyskouski
 */

var gulp = require('gulp');
var sass = require('gulp-sass');
// var rigger = require('gulp-rigger');
var autoprefixer = require('gulp-autoprefixer');

gulp.task('sass', function () {
  return gulp.src(['public/sass/**/*.scss', '!public/sass/values/**/*.scss'])
//    .pipe(rigger()) // can be used '//= footer.html' to concat files
    .pipe(sass({errLogToConsole: true}))
    .pipe(autoprefixer({
        browsers: [
            'last 2 versions', 'Firefox ESR', 'chrome >= 51',
            'iOS 6', 'IE >= 6', 'Firefox >= 15', 'Opera >= 9'/*,
            'Android', 'BlackBerry', 'OperaMobile', 'OperaMini'*/
        ],
        cascade: false
    }))
    .pipe(gulp.dest('public/css'));
});