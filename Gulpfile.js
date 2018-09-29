'use strict';
// Plugins / Functions / Modules
const env = process.env.NODE_ENV;
const plugins = require('gulp-load-plugins')({
    pattern: ['*', '!gulp', '!gulp-load-plugins'],
    rename: {
        'browser-sync': 'browserSync',
        'fs-extra': 'fs',
        'gulp-multi-dest': 'multiDest',
        'js-yaml': 'yaml',
        'marked-terminal': 'markedTerminal',
        'merge-stream': 'mergeStream',
        'postcss-reporter': 'reporter',
        'run-sequence': 'runSequence',
        'gulp-clean-css': 'cleanCSS',
        'gulp-base64': 'base64'
    }
});
const config = {};
const path = process.platform == 'linux' || process.platform == 'darwin' ? '/' : '\\';
config.projectPath = (plugins.fs.realpathSync('./') + '/').replace(/\\/g, '/');
config.tempPath = config.projectPath + 'var/view_preprocessed/pub/static';
config.themes = require('./dev/tools/gulp/helper/config-loader')('themes.json', plugins, config, false);
config.systemPath = path;
plugins.errorMessage = require('./dev/tools/gulp/helper/error-message')(plugins);
plugins.getThemes = require('./dev/tools/gulp/helper/get-themes')(plugins, config);

// Tasks loading
require('gulp-task-loader')({
    dir: ('dev/tools/gulp/task').replace(/\\/g, '/'),
    plugins: plugins,
    configs: config
});
