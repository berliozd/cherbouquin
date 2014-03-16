/**
 * Created by Didier on 16/03/14.
 */
module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        less: {
            default: {
                files: {
                    'public/Resources/css/share1book.css': 'public/Resources/css/share1book.less'
                }
            }

        },
        cssmin: {
            combine: {
                files: {
                    'public/Resources/css/share1book-min.css': ['public/Resources/css/share1book.css']
                }
            }

        },
        watch: {
            styles: {
                files: [ 'public/Resources/css/**/*.less' ],
                tasks: ['less', 'cssmin']
            }
        }


    });

    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Default task(s).
    grunt.registerTask('default', ['watch']);

};