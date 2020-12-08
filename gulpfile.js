const gulp = require('gulp');
const concat = require('gulp-concat');
const minify = require('gulp-minify');
const babel = require('gulp-babel');
const cleanCSS = require('gulp-clean-css');
const minimist = require('minimist');

const options = minimist(process.argv.slice(2), {string: ['src', 'dist']});
const destDir = options.dist.substring(0, options.dist.lastIndexOf("/"));
const destFile = options.dist.replace(/^.*[\\\/]/, '');

console.log('Destination: ' + destDir + '/' + destFile);

gulp.task('compress-js', function () {
    return gulp.src(options.src)
        .pipe(babel({
            plugins: ['transform-react-jsx']
        }))
        .pipe(concat(destFile))
        .pipe(minify({
            ext: {
                min: '.js'
            },
            noSource: true
        }))
        .pipe(gulp.dest(destDir));
    }
);

gulp.task('compress-css', function () {
    return gulp.src(options.src)
        .pipe(concat(destFile))
        .pipe(cleanCSS())
        .pipe(gulp.dest(destDir));
});
