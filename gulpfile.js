/* ----------------------------------------------------------
  Modules
---------------------------------------------------------- */

/* Tools */
const gulp = require('gulp');
const {series} = gulp;
const bs = require('browser-sync');

/* ----------------------------------------------------------
  Config
---------------------------------------------------------- */

const p = require('./package.json');

/* Files & Folders
-------------------------- */

const app_folder = './';
const src_folder = './';
const sass_folder = src_folder + 'scss';
const css_folder = app_folder + 'css';
const sass_files = [sass_folder + '/**.scss', sass_folder + '/**/**.scss'];

/* ----------------------------------------------------------
  Compile styles
---------------------------------------------------------- */

style = require("./intestarter_gulpfile/tasks/style")(sass_files, css_folder, bs, p);
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
