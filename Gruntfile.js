module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        
        clucklesBuild: "Resources/public/Cluckles/build",
        views: "Resources/views/Default",
        
        copy: {
            main: {
                files: [
                    { src: "<%= clucklesBuild %>/example/component.html", dest: "<%= views %>/components.html.twig" },
                    { src: "<%= clucklesBuild %>/example/editor.html", dest: "<%= views %>/editor.html.twig" }
                ]
            }
        }
    });
    
    grunt.loadNpmTasks('grunt-contrib-copy');
    
    grunt.registerTask('default', ['copy']);
};
