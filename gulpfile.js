'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var mainBowerFiles = require('main-bower-files');
var minifyCss = require('gulp-minify-css');
var sourcemaps = require('gulp-sourcemaps');
var ghPages = require('gulp-gh-pages');
var del = require('del');

const Writable = require('stream').Writable;
const buildDir = 'var/build';
const baseResourcesDir = 'src/AppBundle/Resources';
const sassDir = baseResourcesDir + '/sass';
const distDir = 'web/';

// Bower assets
gulp.task('bower', function moveBowerDeps() {
    return gulp.src(mainBowerFiles(), {base: 'bower_components'})
        .pipe(gulp.dest(buildDir+'/lib'));
});

gulp.task('bootstrap:customize', ['bower'], function () {
    gulp.src(sassDir + '/bootstrap/*.scss')
        .pipe(gulp.dest(buildDir + '/lib/bootstrap-sass/assets/stylesheets/bootstrap/'));
    return gulp.src(sassDir + '/*.scss')
        .pipe(gulp.dest(buildDir + '/lib/bootstrap-sass/assets/stylesheets/'));
});

gulp.task('bootstrap:js', ['bootstrap:customize'], function () {
    return gulp.src(buildDir + '/lib/bootstrap-sass/assets/javascripts/*')
        .pipe(gulp.dest(distDir + '/js'));
});

gulp.task('jquery', ['bootstrap:customize'], function () {
    return gulp.src(buildDir + '/lib/jquery/dist/*')
        .pipe(gulp.dest(distDir + '/js'));
});

gulp.task('cssimg', ['bootstrap:customize'], function () {
    return gulp.src(sassDir + '/img/*')
        .pipe(gulp.dest(distDir + '/css/img'));
});

gulp.task('sass', ['bootstrap:customize'], function () {
    return gulp.src(buildDir + '/lib/bootstrap-sass/assets/stylesheets/ita-bootstrap.scss')
        .pipe(sass.sync({precision: 8}).on('error', sass.logError))
        .pipe(minifyCss({compatibility: 'ie8'}))
        .pipe(gulp.dest(distDir + '/css'));
});

gulp.task('dist', ['bootstrap:js', 'jquery', 'sass', 'cssimg'], function () {
});

gulp.task('default', ['dist'], function () {
});

gulp.task('clean', function () {
    return del.sync([distDir + '/*', '!dist/.git'], {dot: true});
});

gulp.task('watch', function () {
    gulp.watch(sassDir + '/**/*.scss', [sassDir + '']);
});

/* ============
 *
 *	Publish to
 *		https://italia-it.github.io/ita-bootstrap
 */
/*gulp.task('deploy', [distDir+''], function () {
 return gulp.src(['./dist/!**!/!*'])
 .pipe(ghPages());
 });*/
