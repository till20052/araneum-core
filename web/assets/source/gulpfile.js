var args        = require('yargs').argv,
    path        = require('path'),
    flip        = require('css-flip'),
    through     = require('through2'),
    gulp        = require('gulp'),
    $           = require('gulp-load-plugins')(),
    gulpsync    = $.sync(gulp),
    PluginError = $.util.PluginError,
    del         = require('del');

// production mode (see build task)
var isProduction = false;
// styles sourcemaps
var useSourceMaps = false;

// Switch to sass mode. 
// Example:
//    gulp --usesass
var useSass = args.usesass;

// Angular template cache
// Example:
//    gulp --usecache
var useCache = args.usecache;

// ignore everything that begins with underscore
var hidden_files = '**/_*.*';
var ignored_files = '!'+hidden_files;

// MAIN PATHS
var paths = {
  build:     '../build/',
  styles:  'less/',
  scripts: 'js/',
  img: 'img/',
  template: 'html/'
}

// VENDOR CONFIG
var vendor = {
  // vendor scripts required to start the app
  base: {
    source: require('./vendor.base.json'),
    dest: '../build/js',
    name: 'base.js'
  },
  // vendor scripts to make the app work. Usually via lazy loading
  build: {
    source: require('./vendor.json'),
    dest: '../vendor'
  }
};


// SOURCES CONFIG 
var source = {
  scripts: [paths.scripts + 'app.module.js',
            // template modules
            paths.scripts + 'modules/**/*.module.js',
            paths.scripts + 'modules/**/*.js',
            // custom modules
            paths.scripts + 'custom/**/*.module.js',
            paths.scripts + 'custom/**/*.js'
  ],
  styles: {
    build:    [ paths.styles + '*.*'],
    themes: [ paths.styles + 'themes/*'],
    watch:  [ paths.styles + '**/*', '!'+paths.styles+'themes/*']
  },
  img: {
    build: [ paths.img + '/**/*.*']
  },
  template: {
      build: [ paths.template + '/**/*.*']
  }

};

// BUILD TARGET CONFIG 
var build = {
  scripts: paths.build + 'js',
  styles:  paths.build + 'css',
  img:  paths.build + 'img',
  template:  paths.build + 'html'
};

// PLUGINS OPTIONS

var prettifyOpts = {
  indent_char: ' ',
  indent_size: 3,
  unformatted: ['a', 'sub', 'sup', 'b', 'i', 'u', 'pre', 'code']
};

var vendorUglifyOpts = {
  mangle: {
    except: ['$super'] // rickshaw requires this
  }
};

var compassOpts = {
  project: path.join(__dirname, '../'),
  css: 'build/css',
  sass: 'source/sass/',
  image: 'build/img'
};

var compassOptsThemes = {
  project: path.join(__dirname, '../'),
  css: 'build/css',
  sass: 'source/sass/themes/', // themes in a subfolders
  image: 'build/img'
};

//---------------
// TASKS
//---------------


// JS APP
gulp.task('scripts:build', function() {
    log('Building scripts..');
    // Minify and copy all JavaScript (except vendor scripts)
    return gulp.src(source.scripts)
        .pipe($.jsvalidate())
        .on('error', handleError)
        .pipe( $.if( useSourceMaps, $.sourcemaps.init() ))
        .pipe($.concat( 'build.js' ))
        .pipe($.ngAnnotate())
        .on('error', handleError)
        .pipe( $.if(isProduction, $.uglify({preserveComments:'some'}) ))
        .on('error', handleError)
        .pipe( $.if( useSourceMaps, $.sourcemaps.write() ))
        .pipe(gulp.dest(build.scripts));
});


// VENDOR BUILD
gulp.task('vendor', gulpsync.sync(['vendor:base', 'vendor:build']) );

// Build the base script to start the application from vendor assets
gulp.task('vendor:base', function() {
    log('Copying base vendor assets..');
    return gulp.src(vendor.base.source)
        .pipe($.expectFile(vendor.base.source))
        .pipe($.if( isProduction, $.uglify() ))
        .pipe($.concat(vendor.base.name))
        .pipe(gulp.dest(vendor.base.dest))
        ;
});

// copy file from bower folder into the app vendor folder
gulp.task('vendor:build', function() {
  log('Copying vendor assets..');

  var jsFilter = $.filter('**/*.js');
  var cssFilter = $.filter('**/*.css');

  return gulp.src(vendor.build.source, {base: 'bower_components'})
      .pipe($.expectFile(vendor.build.source))
      .pipe(jsFilter)
      .pipe($.if( isProduction, $.uglify( vendorUglifyOpts ) ))
      .pipe(jsFilter.restore())
      .pipe(cssFilter)
      .pipe($.if( isProduction, $.minifyCss() ))
      .pipe(cssFilter.restore())
      .pipe( gulp.dest(vendor.build.dest) );

});

// APP LESS
gulp.task('styles:build', function() {
    log('Building application styles..');
    return gulp.src(source.styles.build)
        .pipe( $.if( useSourceMaps, $.sourcemaps.init() ))
        .pipe( useSass ? $.compass(compassOpts) : $.less() )
        .on('error', handleError)
        .pipe( $.if( isProduction, $.minifyCss() ))
        .pipe( $.if( useSourceMaps, $.sourcemaps.write() ))
        .pipe(gulp.dest(build.styles));
});

// APP RTL
gulp.task('styles:build:rtl', function() {
    log('Building application RTL styles..');
    return gulp.src(source.styles.build)
        .pipe( $.if( useSourceMaps, $.sourcemaps.init() ))
        .pipe( useSass ? $.compass(compassOpts) : $.less() )
        .on('error', handleError)
        .pipe(flipcss())
        .pipe( $.if( isProduction, $.minifyCss() ))
        .pipe( $.if( useSourceMaps, $.sourcemaps.write() ))
        .pipe($.rename(function(path) {
            path.basename += "-rtl";
            return path;
        }))
        .pipe(gulp.dest(build.styles));
});

// LESS THEMES
gulp.task('styles:themes', function() {
    log('Building application theme styles..');
    return gulp.src(source.styles.themes)
        .pipe( useSass ? $.compass(compassOptsThemes) : $.less() )
        .on('error', handleError)
        .pipe(gulp.dest(build.styles));
});

// IMAGES
gulp.task('image:build', function () {
    log('Compressing application images..');
    return gulp.src(source.img.build)
        .pipe(gulp.dest(build.img));
});

// IMAGES
gulp.task('html:build', function () {
    log('Coping html templates..');
    return gulp.src(source.template.build)
        .pipe(gulp.dest(build.template));
});

//---------------
// WATCH
//---------------

// Rerun the task when a file changes
gulp.task('watch', function() {
  log('Starting watch and LiveReload..');

  $.livereload.listen();

  gulp.watch(source.scripts,         ['scripts:build']);
  gulp.watch(source.styles.watch,    ['styles:build', 'styles:build:rtl']);
  gulp.watch(source.styles.themes,   ['styles:themes']);
  gulp.watch(source.img.build,       ['image:build']);
  gulp.watch(source.template.build,  ['html:build']);

  // a delay before triggering browser reload to ensure everything is compiled
  var livereloadDelay = 1500;
  // list of source file to watch for live reload
  var watchSource = [].concat(
      source.scripts,
      source.styles.watch,
      source.styles.themes,
      source.img.build,
      source.template.build
    );

  gulp
    .watch(watchSource)
    .on('change', function(event) {
      setTimeout(function() {
        $.livereload.changed( event.path );
      }, livereloadDelay);
    });

});

// lint javascript
gulp.task('lint', function() {
    return gulp
        .src(source.scripts)
        .pipe($.jshint())
        .pipe($.jshint.reporter('jshint-stylish', {verbose: true}))
        .pipe($.jshint.reporter('fail'));
});

// Remove all files from the build paths
gulp.task('clean', function(done) {
    var delconfig = [].concat(
                        build.styles,
                        build.scripts,
                        vendor.build.dest
                      );

    log('Cleaning: ' + $.util.colors.blue(delconfig));
    // force: clean files outside current directory
    del(delconfig, {force: true}, done);
});

//---------------
// MAIN TASKS
//---------------

// build for production (minify)
gulp.task('build', gulpsync.sync([
          'prod',
          'vendor',
          'assets'
        ]));

gulp.task('prod', function() { 
  log('Starting production build...');
  isProduction = true; 
});

// build with sourcemaps (no minify)
gulp.task('sourcemaps', ['usesources', 'default']);
gulp.task('usesources', function(){ useSourceMaps = true; });

// default (no minify)
gulp.task('default', gulpsync.sync([
          'vendor',
          'assets',
          'watch'
        ]), function(){

  log('************');
  log('* All Done * You can start editing your code, LiveReload will update your browser after any change..');
  log('************');

});

gulp.task('assets',[
          'scripts:build',
          'styles:build',
          'styles:build:rtl',
          'styles:themes',
          'image:build',
          'html:build'
        ]);


/////////////////////


// Error handler
function handleError(err) {
  log(err.toString());
  this.emit('end');
}

// Mini gulp plugin to flip css (rtl)
function flipcss(opt) {
  
  if (!opt) opt = {};

  // creating a stream through which each file will pass
  var stream = through.obj(function(file, enc, cb) {
    if(file.isNull()) return cb(null, file);

    if(file.isStream()) {
        // Todo: isStream!
    }

    var flippedCss = flip(String(file.contents), opt);
    file.contents = new Buffer(flippedCss);
    cb(null, file);
  });

  // returning the file stream
  return stream;
}

// log to console using 
function log(msg) {
  $.util.log( $.util.colors.blue( msg ) );  
}
