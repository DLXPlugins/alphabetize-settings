module.exports = function( grunt ) {
	grunt.initConfig( {
		compress: {
			main: {
				options: {
					archive: 'sortacular.zip',
				},
				files: [
					{ src: [ 'sortacular.php' ], dest: '/', filter: 'isFile' },
					{ src: [ 'readme.txt' ], dest: '/', filter: 'isFile' },
				],
			},
		},
	} );
	grunt.registerTask( 'default', [ 'compress' ] );

	grunt.loadNpmTasks( 'grunt-contrib-compress' );
};