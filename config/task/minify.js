/**
 * Minify files
 *
 * @author Viachaslau Lyskouski
 */

// Specify public directory with css, js folders
var publicDir = 'public/';

// Gulp initialization
var gulp = require('gulp');
var runSequence = require('run-sequence');
var change = require('gulp-change');
var del = require('del');
var min = {
    js: require('gulp-uglify'),
    css: require('gulp-cssmin')
};
var fs = require('fs');
var gulpIgnore = require('gulp-ignore');
var rename = require("gulp-rename");
var concat = require('gulp-concat');
var gzip = require('gulp-gzip');
var babel = require('gulp-babel');

// Sources (clone to avoid update)
var sources = JSON.parse(JSON.stringify(require('./params/minify.json')));
// Attach extends
for (var k in sources) {
    sources[k]['js'].forEach(function(path, pos) {
        try {
            var minPath = path.replace('.js', '.min.js').replace('!', '').replace('*', '');
            fs.statSync(publicDir + '/js/' + minPath);
            console.log('+ min.version:', path);
            sources[k]['js'][pos] = minPath;
        } catch (err) {
            // Ignore errors
        }
    });
    if (typeof sources[k]['@extend'] !== 'undefined') {
        sources[k]['js'] = sources[sources[k]['@extend']]['js'].concat(sources[k]['js']);
        sources[k]['css'] = sources[sources[k]['@extend']]['css'].concat(sources[k]['css']);
        delete sources[k]['@extend'];
    }
}
// Extra evaluations for the list
for (var k in sources) {
    // Move *files to end
    sources[k]['js'].forEach(function (path, pos) {
        if (path.indexOf('*') === 0) {
            sources[k]['js'].splice(pos, 1);
            sources[k]['js'].push(path.replace('*', ''));
        }
    });
    // Unify values inside list
    sources[k]['js'] = Array.from(new Set(sources[k]['js']));
}
// Files group extension
var ext = '';

// Minify JavaScript
gulp.task('js-init', function (callback) {
    ext = 'js';
    callback();
});
gulp.task('js', function (callback) {
    return runSequence(
        'js-init',
        'js-libs',
        'cleanup',
        ['minify', 'minify-all'],
        'copy-min',
        'copy-min-origin',
        'minify-gzip',
        callback
    );
});

/**
 * minify.json
            "!lib/npm/polyfill.js",
            "!lib/npm/system.js",
            "!lib/npm/reflect.js",
 */
gulp.task('js-libs', function(){
    return gulp.src([
            'node_modules/systemjs/dist/system.js',
            'node_modules/babel-polyfill/dist/polyfill.js',
            'node_modules/harmony-reflect/reflect.js'
        ])
        .pipe(gulp.dest(publicDir + 'js/lib/npm'));
});


// Minify CSS
gulp.task('css-init', function (callback) {
    ext = 'css';
    callback();
});
gulp.task('css', function (callback) {
    return runSequence(
        'css-init',
        'cleanup',
        ['minify', 'minify-all'],
        'copy-min',
        'copy-min-origin',
        'minify-gzip',
        callback
    );
});

// Clear directory
gulp.task('cleanup', function () {
    if (!ext) {
        throw Error('Missing extension! Use: gulp (js|css)');
    }
    console.log('[!] Cleanup: ', ext);

    return del([
        publicDir + ext + '.min/**/*'
    ]);
});

// Compress minified files
gulp.task('minify-gzip', function () {
    if (!ext) {
        throw Error('Missing extension! Use: gulp (js|css)');
    }
    console.log('[!] Compress ALL: ', ext);

    var folder = publicDir + ext + '.min';
    return gulp.src(folder + '/**/*.' + ext)
            .pipe(gzip())
            .pipe(gulp.dest(folder));
});

// Minify all files inside the folder
gulp.task('minify-all', function () {
    if (!ext) {
        throw Error('Missing extension! Use: gulp (js|css)');
    }
    console.log('[!] Minify ALL: ', ext);

    var folder = publicDir + ext;

    // @todo get list of files and exclude already minified

    var pr = gulp.src(folder + '/**/*')
        .pipe(gulpIgnore.exclude('*.min.' + ext));
    // Special cases for JavaScript
    if (ext === 'js') {
        pr.pipe(babel({
            presets: ["es2015"]
        })).pipe(change(function (content) {
            return content.split('/img/').join('/img.min/');
        }));
    } else if (ext === 'css') {
        pr.pipe(change(function (content) {
            return content.split('/img/').join('/img.min/');
        }));
    }
    // Compress files (exclude .min.js versions)
    pr.pipe(min[ext]())
        .pipe(gulp.dest(folder + '.min/' + sources.default.version));
    console.log('Compress files (exclude .min.' + ext + ' versions)');
    return pr;
});

// Copy already minified files
gulp.task('copy-min', function() {
    if (!ext) {
        throw Error('Missing extension! Use: gulp (js|css)');
    }
    var folder = publicDir + ext;
    console.log('Copied minified files');
    // Copy minified files (just in case of their usages)
    gulp.src(folder + '/**/*.min.*')
        .pipe(gulp.dest(folder + '.min/' + sources.default.version));

    console.log('Minified files on place of originals');
    // Minified files on place of originals
    return gulp.src(folder + '/**/*.min.*')
        .pipe(rename(function (path) {
            path.basename = path.basename.replace('.min', '');
        }))
        .pipe(gulp.dest(folder + '.min/' + sources.default.version));
});
gulp.task('copy-min-origin', function() {
    if (!ext) {
        throw Error('Missing extension! Use: gulp (js|css)');
    }
    var folder = publicDir + ext;
    console.log('Copied minified files');
    // Copy minified files (just in case of their usages)
    return gulp.src(folder + '/**/*.min.*')
        .pipe(gulp.dest(folder + '.min/' + sources.default.version));
});

// Common task to min/concat files
gulp.task('minify', function (callback) {
    // var ext = extBuild.pop();
    if (!ext) {
        throw Error('Missing extension! Use: gulp (js|css)');
    }
    console.log('[!] Minify: ', ext);

    var folder = publicDir + ext;
    var minFolder = folder + '.min';
    for (var name in sources) {
        var list = [];
        // Bind list
        console.log('Concat ' + ext + ' `' + name + '`:');
        sources[name][ext].forEach(function (path) {
            var tgt = (folder + '/' + path.replace('!', '')).split('/');
            while (~tgt.indexOf('..')) {
                tgt.splice(tgt.indexOf('..') - 1, 2);
            }
            console.log('- ', tgt.join('/'));
            list.push(tgt.join('/'));
        });

        // Join files
        var pr = gulp.src(list);
        //if (ext === 'js') {
        //    pr.pipe(babel({
        //        "presets": ["es2015"]
        //    }));
        //}
        // Concatenate files
        pr.pipe(concat(name + '.' + ext))
            // Minify list of files
            .pipe(min[ext]())
            // Update images path
            .pipe(change(function (content) {
                return content.split('/img/').join('/img.min/');
            }))
            // Destination folder
            .pipe(gulp.dest(minFolder));
    }
    // This is what lets gulp know this task is complete!
    callback();

});