
var es = require('event-stream');
var gulp = require('gulp');
var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');

var libPath = 'resources/';

var jsDeps = [
    { srcGlob: 'node_modules/bootstrap/dist/js/bootstrap.js', dest: libPath+'bootstrap/js' },
    { srcGlob: 'node_modules/inputmask/dist/jquery.inputmask.bundle.js', dest: libPath+'inputmask' },
    { srcGlob: 'node_modules/jquery/dist/jquery.js', dest: libPath+'jquery' },
    { srcGlob: 'node_modules/punycode/punycode.js', dest: libPath+'punycode' },
    { srcGlob: 'node_modules/yii2-pjax/jquery.pjax.js', dest: libPath+'yii2-pjax' }
];

var staticDeps = [
    { srcGlob: 'node_modules/bootstrap/dist/css/*', dest: libPath+'bootstrap/css' },
    { srcGlob: 'node_modules/bootstrap/dist/fonts/*', dest: libPath+'bootstrap/fonts' }
];

gulp.task('deps', function() {
    var streams = [];

    // Minify & move the JS deps
    jsDeps.forEach(function(dep) {
        streams.push(
            gulp.src(dep.srcGlob)
            //.pipe(gulp.dest(dest))
                .pipe(sourcemaps.init())
                .pipe(uglify())
                //.pipe(rename({ suffix: '.min' }))
                .pipe(sourcemaps.write('./'))
                .pipe(gulp.dest(dep.dest))
        );
    });

    // Statically move over any dep files we don't need to modify
    staticDeps.forEach(function(dep) {
        streams.push(
            gulp.src(dep.srcGlob)
                .pipe(gulp.dest(dep.dest))
        );
    });

    return es.merge(streams);
});