/*
 * grunt-clear
 * https://github.com/geddski/grunt-clear
 *
 * Copyright (c) 2012 Dave Geddes
 * Licensed under the MIT license.
 */
'use strict';

module.exports = function(grunt) {
  grunt.registerTask('clear', 'Clear your terminal window', function() {
    if(process.platform === "win32"){
      process.stdout.write('\x1Bc');
    }
    //clear with no flicker on unix
    else{
      var done = this.async();
      var child = require('child_process');
      var ps = child.spawn('clear');
      ps.stdout.pipe(process.stdout);
      ps.on('exit', done);
      ps.stdin.end();
    }
  });
};