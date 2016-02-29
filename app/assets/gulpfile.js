var gulp            = require('gulp');
var sass            = require('gulp-sass');
var cssnano         = require('gulp-cssnano');
var autoprefixer    = require('gulp-autoprefixer');

gulp.task('default', [
    'sass'
], function() {});

gulp.task('sass', function() {
    return gulp.src('./sass/*.scss')
            .pipe(sass().on('error', console.log))
            .pipe(autoprefixer({
                browsers: ['last 3 version'],
                cascade: false
            }))
            .pipe(cssnano())
            .pipe(gulp.dest('./css/'));
});
