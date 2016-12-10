# grunt-clear
Clears your command line. Automate all the things. 

## Getting Started
Install this grunt plugin next to your project's [Gruntfile][getting_started] with: `npm install grunt-clear`

Then add this line to your project's Gruntfile:

```javascript
grunt.loadNpmTasks('grunt-clear');
```

[grunt]: https://github.com/cowboy/grunt
[getting_started]: https://github.com/cowboy/grunt/blob/master/docs/getting_started.md

## Documentation
Turn your console output into a live dashboard by clearing it before displaying new results. 
Add this task as the **first item** of your `watch` task:

```javascript
watch: {
  clear: {
    //clear terminal on any watch task. beauty.
    files: ['**/*'], //or be more specific
    tasks: ['clear']
  }
}
```

The `watch` task will run things in order, so make sure `clear` is very first otherwise your console will clear the output of other tasks you are probably interested in. 

## Contributing
In lieu of a formal styleguide, take care to maintain the existing coding style. Add unit tests for any new or changed functionality. Lint and test your code using [grunt][grunt].

## Todo 
Write tests
Make sure it works on linux
Do screencast

## License
Copyright (c) 2012 Dave Geddes  
Licensed under the MIT license.
