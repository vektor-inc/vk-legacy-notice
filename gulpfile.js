const gulp = require('gulp')

gulp.task('dist', function (done) {
	const files = gulp.src(
	  [
		'./admin/**',
		'./inc/**',
		'./languages/**',
		"./vendor/**",		
		'./*.php',
		'./*.txt',
	  ], {
		base: './'
	  }
	)
	files.pipe(gulp.dest("dist/vk-legacy-notice"));
	done();
  });