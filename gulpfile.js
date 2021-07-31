/* ----------------------------------------------------------
  Modules
---------------------------------------------------------- */

const p = require('./package.json');

/* Tools */
const gulp = require('gulp');
const {series} = gulp;

/* Reload */

/* Sass */
const sass = require('gulp-sass')(require('node-sass'));
const sassGlob = require('gulp-sass-glob');
const autoprefixer = require('gulp-autoprefixer');
const stripCssComments = require('gulp-strip-css-comments');
const removeEmptyLines = require('gulp-remove-empty-lines');
const trimlines = require('gulp-trimlines');
const gulpStylelint = require('gulp-stylelint');

/* Icon font */
const runTimestamp = function() {
    return Math.round(Date.now() / 1000);
};
const replace = require('gulp-replace');

/* ----------------------------------------------------------
  Config
---------------------------------------------------------- */

const project_name = 'project';
const app_folder = './';
const src_folder = './';
const sass_folder = src_folder + 'scss';
const sass_folder_proj = sass_folder + '/' + project_name;
const css_folder = app_folder + 'css';
const sass_files = [sass_folder + '/**.scss', sass_folder + '/**/**.scss'];


/* ----------------------------------------------------------
  Compile styles
---------------------------------------------------------- */

function style() {
    return gulp.src(sass_files)
        .pipe(sassGlob())
        .pipe(sass({
            outputStyle: 'compact',
            indentType: 'space',
            indentWidth: 0
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(stripCssComments({
            whitespace: false
        }))
        .pipe(removeEmptyLines())
        .pipe(trimlines())
        .pipe(replace(/( ?)([\,\:\{\}\;\>])( ?)/g, '$2'))
        .pipe(replace(';}', '}'))
        .pipe(gulp.dest(css_folder, {
            sourcemaps: false
        }))
        .pipe(gulpStylelint({
            failAfterError: false,
            reporters: [{
                formatter: 'string',
                console: true
            }],
            debug: false
        }));
}
exports.style = style;

/* ----------------------------------------------------------
  Watch
---------------------------------------------------------- */

exports.watch = function watch() {
    style();
    return gulp.watch(sass_files, style);
};

/* ----------------------------------------------------------
  Default
---------------------------------------------------------- */

const defaultTask = series(style);

exports.default = defaultTask;
