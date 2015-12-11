'use strict';
var gulp = require('gulp'),
    autoprefixer = require('autoprefixer'),
    $ = require('gulp-load-plugins')({camelize: true}),
    AUTOPREFIXER_BROWSERS = [
        'ie >= 9',
        'ie_mob >= 10',
        'ff >= 30',
        'chrome >= 34',
        'safari >= 7',
        'opera >= 23',
        'ios >= 7',
        'android >= 4.4',
        'bb >= 10'
    ];


var handleError = function (task) {
    return function (err) {
        $.notify.onError({
            message: task + ' failed, check the logs..' + err,
            sound: false
        })(err);
    };
};
// --------------------------
// CUSTOM TASK METHODS
// --------------------------
var tasks = {

    // --------------------------
    // Copy static assets
    // --------------------------
    assets: function () {
        return gulp.src('./src/img/**/*')
            .pipe(gulp.dest('img/'));
    },
    // --------------------------
    // SASS (libsass)
    // --------------------------
    sass: function () {
        return gulp.src('./src/scss/*.scss')
            .pipe($.sass({
                sourceComments: false,
                outputStyle: 'compressed'
            }))
            .on('error', handleError('SASS'))
            // autoprefixer
            .pipe($.postcss([autoprefixer({browsers: AUTOPREFIXER_BROWSERS})]))
            // give it a file and save
            .pipe(gulp.dest('css'));
    },
    // --------------------------
    // linting
    // --------------------------
    lintjs: function () {
        return gulp.src([
            'gulpfile.js',
            './src/js/**/*.js'
        ])
        .pipe($.jshint())
        .pipe($.jshint.reporter($.stylish));
    },
    // --------------------------
    // Optimize asset images
    // --------------------------
    optimize: function () {
        return gulp.src('./src/img/*.{gif,jpg,png,svg}')
            .pipe($.imagemin({
                progressive: true,
                svgoPlugins: [{removeViewBox: false}],
                // png optimization
                optimizationLevel: 3
            }))
            .pipe(gulp.dest('./img/'));
    },

    minifyjs: function() {
        return gulp.src(['./src/js/vendor/*.js','./src/js/audio-player.js'])
            .pipe($.concat('./js/oog-media-player.min.js'))
            .pipe($.uglify())
            .pipe(gulp.dest('./'));
    }

};

// --------------------------
// CUSTOMS TASKS
// --------------------------
gulp.task('clean', tasks.clean);
// individual tasks
gulp.task('assets', ['clean'], tasks.assets);
gulp.task('sass', ['clean'], tasks.sass);
gulp.task('lint:js', tasks.lintjs);
gulp.task('minify:js', tasks.minifyjs);
gulp.task('optimize', tasks.optimize);

// --------------------------
// DEV/WATCH TASK
// --------------------------
gulp.task('watch', function () {

    // --------------------------
    // watch:sass
    // --------------------------
    gulp.watch('./src/scss/**/*.scss', ['build']);

    // --------------------------
    // watch:js
    // --------------------------
    gulp.watch('./src/js/**/*.js', ['build']);

});

// build task
gulp.task('build', [
    'clean',
    'assets',
    'minify:js',
    'sass'
]);

gulp.task('default', ['watch']);