'use strict';
module.exports = function() { // eslint-disable-line func-names
  // Global variables
  const plugins = this.opts.plugins,
        config  = this.opts.configs;

  // Prevent runing inheritance task more than once
  plugins.util.env.pipeline = true;

  plugins.runSequence('inheritance', 'babel', 'styles', () => {
    plugins.util.log(
      plugins.util.colors.green('Gulp compile is done successful!')
    );
  });
};
