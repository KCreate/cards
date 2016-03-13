var gulp            = require('gulp');
var sass            = require('gulp-sass');
var cssnano         = require('gulp-cssnano');
var autoprefixer    = require('gulp-autoprefixer');

gulp.task('default', [
    'sass',
    'sass:watch'
], function() {});


// Compile, autoprefix and minify sass files
gulp.task('sass', function() {
    return gulp.src('./app/assets/sass/*.scss')
        .pipe(sass().on('error', console.log))
        .pipe(autoprefixer({
            browsers: ['last 3 version'],
            cascade: false
        }))
        .pipe(cssnano())
        .pipe(gulp.dest('./app/assets/css/'));
});

// Watch the sass directory
gulp.task('sass:watch', function() {
    return gulp.watch(['./app/assets/sass/*.scss'], ['sass']);
});
