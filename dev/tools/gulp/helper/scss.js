'use strict';
module.exports = function(gulp, plugins, config, name, file) { // eslint-disable-line func-names
  const theme         = config.themes[name],
        srcBase       = config.projectPath + 'var/view_preprocessed/pub/static' + theme.dest.replace('pub/static', ''),
        stylesDir     = theme.stylesDir ? theme.stylesDir : 'styles',
        dest          = [],
        disableMaps   = plugins.util.env.disableMaps || false,
        production    = plugins.util.env.prod || false,
        postcss       = [],
        disableSuffix = theme.disableSuffix || false;

  if (theme.postcss) {
    theme.postcss.forEach(el => {
      postcss.push(eval(el));
    });
  }
  else {
    postcss.push(plugins.autoprefixer({
      browsers: ['last 2 versions', 'Firefox > 20'],
      cascade: true,
      remove: true
    }));
  }

  function adjustDestinationDirectory(file) {
    if (file.dirname.startsWith(stylesDir)) {
      file.dirname = file.dirname.replace(stylesDir, 'css');
    }
    else {
      file.dirname = file.dirname.replace('/' + stylesDir, '');
    }
    return file;
  }

  theme.locale.forEach(locale => {
    dest.push(config.projectPath + theme.dest + '/' + locale);
  });

  return gulp.src(
    file || srcBase + '/**/*.scss',
    { base: srcBase }
  )
    .pipe(
      plugins.if(
        !plugins.util.env.ci,
        plugins.plumber({
          errorHandler: plugins.notify.onError('Error: <%= error.message %>')
        })
      )
    )
    .pipe(plugins.if(!disableMaps, plugins.sourcemaps.init()))
    .pipe(
      plugins.sass()
        .on('error', plugins.sassError.gulpSassError(plugins.util.env.ci || false))
    )
    .pipe(plugins.cleanCSS({
        advanced: true,
        keepBreaks: false,
        keepSpecialComments: '*'
    }))
    .pipe(plugins.if(!disableMaps, plugins.sourcemaps.write()))
    .pipe(plugins.autoprefixer())
    .pipe(plugins.if(production, plugins.rename({ suffix: '.min' })))
    .pipe(plugins.rename(adjustDestinationDirectory))
    .pipe(plugins.multiDest(dest))
    .pipe(plugins.base64())
    .pipe(plugins.logger({
      display   : 'name',
      beforeEach: 'Theme: ' + name + ' ',
      afterEach : ' Compiled!'
    }))
    .pipe(plugins.browserSync.stream());
};
