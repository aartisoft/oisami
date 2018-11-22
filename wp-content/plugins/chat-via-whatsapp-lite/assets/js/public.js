jQuery(function($) {
	
	'use strict';
	
	
	/**
	 * jQuery Tiny Pub/Sub
	 * https://github.com/cowboy/jquery-tiny-pubsub
	 *
	 * Copyright (c) 2013 "Cowboy" Ben Alman
	 * Licensed under the MIT license.
	 **********************************************************************/
	var o = $({});
	$.subscribe = function() {o.on.apply(o, arguments);};
	$.unsubscribe = function() {o.off.apply(o, arguments);};
	$.publish = function() {o.trigger.apply(o, arguments);};
	
	/**
	 * Save the main building block of DOM elements; for the 
	 * sake of succinctness
	 **********************************************************************/
	var DOM = (function ( dom ) {
		
		var dom = dom || {};
		
		dom.body = $('body:eq(0)');
		
		
		return dom;
		
	}( DOM ) );
	
	/**
	* Centralize the process of hide/show of the box.
	**********************************************************************/
	(function () {
		
		var wptwa = DOM.body.find( '.wptwa-container' ),
			box = wptwa.find( '.wptwa-box' ),
			toggleBox = function ( e ) {
				box.toggleClass( 'show' );
			};
		
		$.subscribe('wptwa-toggle-box', toggleBox);
		
	}());
	
	/**
	* Show and hide the box.
	**********************************************************************/
	(function () {
		
		var wptwaFlag = DOM.body.find( '.wptwa-flag' ),
			wptwa = DOM.body.find( '.wptwa-container' ),
			handler = wptwa.find( '.wptwa-handler' ),
			close = wptwa.find( '.wptwa-close' )
			;
		
		if ( ! wptwa.length || ! wptwaFlag.length ) {
			return;
		}
		
		/* Toggle box on handler's (or close's) click */
		handler.add( close ).on( 'click', function () {
			$.publish('wptwa-toggle-box');
		} );
		
	}());
	
	/**
	* If avatar is not provided or provided but error, add a hint to 
	* .wptwa-face so we can show a default image.
	**********************************************************************/
	(function () {
		
		DOM.body.find( '.wptwa-container .wptwa-face' ).each(function () {
			var el = $( this ),
				img = el.find( 'img' ),
				noImage = true
				;
			
			if ( img.length ) {
				var url = img.attr( 'src' ),
					tester = new Image();
				tester.src = url;
				
				tester.onerror = function () {
					el.addClass( 'no-image' );
				};
				
			}
			else {
				el.addClass( 'no-image' );
			}
			
		});
		
	}());
	
	/**
	* If we're on desktop, use web.whatsapp.com instead.
	**********************************************************************/
	(function () {
		
		var wptwaAccounts = DOM.body.find( '.wptwa-account' ),
			wptwaFlag = DOM.body.find( '.wptwa-flag' )
			;
		
		if ( ! wptwaFlag.length ) {
			return;
		}
		
		/* Change URL to web.whatsapp.com if the user is using a desktop. */
		if ( window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == '"desktop"' || window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == 'desktop' ) {
			
			wptwaAccounts.each(function () {
				var el = $( this ),
					number = el.data( 'number' )
					;
				
				if ( '' === number ) {
					return true;
				}
				el.attr( 'href', 'https://web.whatsapp.com/send?phone=' + number );
			});
			
		}
		
	}());
	
	/**
	* 
	**********************************************************************/
	(function () {
		
		
		
	}());
	
});