const { series, src, dest } = require('gulp');
const concat = require('gulp-concat');
const sass = require('gulp-sass');
const uglify = require('gulp-uglify');
const minify = require('gulp-minify');
//const rename = require('gulp-rename');


// gulp.task('watch', function(){
//     gulp.watch('frontend/web/js/test/*.js', ['min-js']);
//     // Other watchers
// });


function clean(cb) {
    // body omitted
    cb();
}

function build(cb) {
    src(['frontend/web/js/call-widget/*.jsx'])
        // The gulp-uglify plugin won't update the filename
        .pipe(concat('bundle2.jsx'))
        //.pipe(uglify())
        // So use gulp-rename to change the extension
        //.pipe(rename({ extname: '.min.js' }))
        .pipe(dest('frontend/web/js/test/'));
    cb();
}

exports.build = build;
exports.default = series(clean, build);