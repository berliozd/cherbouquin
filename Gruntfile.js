/**
 * Created by Didier on 16/03/14.
 */
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      default: {
        files: {
          'public/Resources/css/main.css': 'styles/main.scss'
        }
      }
    },
    cssmin: {
      combine: {
        files: [
          {'public/Resources/css/main-min.css': ['public/Resources/css/main.css']},
          {'public/Resources/css/bootstrap.min.css': ['styles/vendors/bootstrap.css']}
        ]
      }

    },
    watch: {
      styles: {
        files: ['styles/**/*.scss', 'public/Resources/css/bootstrap.css'],
        tasks: ['sass', 'cssmin']
      }
    }


  });

  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['watch']);

};