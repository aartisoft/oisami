jQuery(function($) {
	
	'use strict';
	
	/**
	 * Save the main building block of DOM elements; for the 
	 * sake of succinctness
	 **********************************************************************/
	var DOM = (function ( dom ) {
		
		var dom = dom || {};
		
		dom.body = $( 'body:eq(0)' );
		
		return dom;
		
	} ( DOM ) );
	
	/**
	* Move an account up or down
	**********************************************************************/
	(function () {
		
		DOM.body.on( 'click', '.wptwa-queue-buttons span', function () {
			
			var el = $( this ),
				direction = el.is( '.wptwa-move-up' ) ? 'up' : 'down',
				table = el.parents( 'table' )
				;
			
			if ( el.is( '.wptwa-move-up' ) ) {
				table.insertBefore( table.prev( 'table' ) );
			}
			else {
				table.insertAfter( table.next( 'table' ) );
			}
			
		} );
		
	}());
	
	/**
	* 
	**********************************************************************/
	(function () {
		
		
		
	}());
	
});
