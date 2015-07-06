var gulp = require('gulp'),
    csscomb = require('gulp-csscomb'),
    csso = require('gulp-csso'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    gzip = require('gulp-gzip');

gulp.task('style', function() {
    return gulp.src('apps/frontend/public/assets/css/demo.css')
        .pipe(csscomb())
        .pipe(gulp.dest('apps/frontend/public/assets/css/'))
        .pipe(csso())
        .pipe(rename({suffix:".min"}))
        .pipe(gulp.dest('apps/frontend/public/assets/css/'))
        .pipe(gzip())
        .pipe(gulp.dest('apps/frontend/public/assets/css/'));
});


gulp.task('js-only', function() {
    return gulp.src([
        'apps/frontend/public/assets/js/app.js'
    ])
        .pipe(uglify())
        .pipe(rename({basename: "app-only", suffix:".min"}))
        .pipe(gulp.dest('apps/frontend/public/assets/js/'))
        .pipe(gzip())
        .pipe(gulp.dest('apps/frontend/public/assets/js/'));
});


gulp.task('js', function() {
    return gulp.src([
        'apps/frontend/public/assets/js/vendor/underscore/underscore.js',
        'apps/frontend/public/assets/js/vendor/angular/angular.min.js',
        'apps/frontend/public/assets/js/vendor/angular-animate/angular-animate.min.js',
        'apps/frontend/public/assets/js/vendor/angular-bootstrap/ui-bootstrap.min.js',
        'apps/frontend/public/assets/js/vendor/angular-bootstrap/ui-bootstrap-tpls.min.js',
        'apps/frontend/public/assets/js/vendor/angular-translate/angular-translate.min.js',
        'apps/frontend/public/assets/js/vendor/angular-rock/angular-rock.min.js',
        'apps/frontend/public/assets/js/app.js'
    ])
        .pipe(concat('app.js'))
        .pipe(uglify())
        .pipe(rename({suffix:".min"}))
        .pipe(gulp.dest('apps/frontend/public/assets/js/'))
        .pipe(gzip())
        .pipe(gulp.dest('apps/frontend/public/assets/js/'));
});