const gulp = require('gulp')
const replace = require('gulp-replace')

gulp.task('dist', function (done) {
	const files = gulp.src(
	  [
		'./**/*.php',
		'./**/*.txt',
		'./**/*.css',
		'./**/*.png',
		'./assets/**',
		'./inc/**',
		'./languages/**',
		"!./vendor/**",
		"!./.vscode/**",
		"!./bin/**",
		"!./dist/**",
		"!./node_modules/**/*.*",
		"!./tests/**",
		"!./dist/**",
	  ], {
		base: './'
	  }
	)
	files.pipe(gulp.dest("dist/vk-legacy-notice"));
	done();
  });

  // replace_text_domain ////////////////////////////////////////////////
gulp.task("replace_text_domain", function(done) {
	// vk-admin
	gulp.src(["./inc/vk-admin/package/*"])
		.pipe(replace("vk_admin_textdomain","vk-legacy-notice"))
		.pipe(gulp.dest("./inc/vk-admin/package/"));
	done();
});