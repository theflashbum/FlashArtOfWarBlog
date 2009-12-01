/* -------------------------------------------------------------------------- */
/*  Thanks to techfoolery for this script which can be found at:              */
/*  http://techfoolery.com/archives/2006/08/11/2021/ (it has been slightly    */
/*  modified for use here.)                                                   */
/*  And of course thanks to Yahoo! for their wonderful YUI.                   */
/* -------------------------------------------------------------------------- */

YAHOO.namespace('smoothscroll');

/* -------------------------------------------------------------------------- */
/*  Functions to initialize and perform the scrolling anchor links.           */
/* -------------------------------------------------------------------------- */

YAHOO.smoothscroll = function () {

	var stepIncrement = 10;	// The number of pixels that each step moves the window.
	var stepDelay = 3;	// The number of milliseconds between steps.
	var limit = 10000;	// After 10 seconds the scroll is killed.
	
	var running = false;
	
	/* Recursive scrolling method. Steps through the complete scroll. */

	function scrollStep(to, dest, down) {

		if(!running || (down && to >= dest) || (!down && to <= dest)) {
			YAHOO.smoothscroll.killScroll();
			return;
		}

		if((down && to >= (dest - (2 * stepIncrement))) ||
		   (!down && to <= (dest - (2 * stepIncrement)))) {
			stepIncrement = stepIncrement * .55;
		}

		window.scrollTo(0, to);

		// Assign the returned function to a public method.
		YAHOO.smoothscroll.nextStep = callNext(+to + stepIncrement, dest, down);
		window.setTimeout(YAHOO.smoothscroll.nextStep, stepDelay);
	}

	/* Create a closure so that scrollStep can be accessed by window.setTimeout(). */

	function callNext(to, dest, down) {

		return function() { scrollStep(to, dest, down); };
	}

	return {
	
		nextStep: null,
		killTimeout: null,
	
		/* Sets up and calls scrollStep. */

		anchorScroll: function(e, obj) {

			var clickedLink = YAHOO.util.Event.getTarget(e);
			var anchorId = clickedLink.href.replace(/^.*#/, '');
			var target = YAHOO.util.Dom.get(anchorId);

			if(target) {	
				YAHOO.util.Event.stopEvent(e);
				running = true;

				var yCoord = ((YAHOO.util.Dom.getY(target) - 6) < 0) ? 0 : YAHOO.util.Dom.getY(target) - 6;
				var currentYPosition = (document.all) ? document.body.scrollTop : window.pageYOffset;
				var down = true;

				if(currentYPosition > yCoord) {
					stepIncrement *= -1;	// Reverse the direction if we are scrolling up.
					down = false;
				}

				// Stop the scroll once the time limit is reached.
				YAHOO.smoothscroll.killTimeout = window.setTimeout(YAHOO.smoothscroll.killScroll, limit);

				// Start the scroll by calling scrollStep().
				scrollStep(currentYPosition + stepIncrement, yCoord, down);	
			}
		},
		
		/* Kill the scroll after a timeout, to prevent an endless loop. */
		
		killScroll: function() {
			window.clearTimeout(YAHOO.smoothscroll.killTimeout);
			running = false;
			stepIncrement = 10;			
		},
	
		/* Attach the scrolling method to the links with the class 'footnote-link'. */

		init: function() {

			var links = YAHOO.util.Dom.getElementsByClassName('footnote-link', 'a');
			YAHOO.util.Event.addListener(links, 'click', YAHOO.smoothscroll.anchorScroll, YAHOO.smoothscroll, true);
			
		}
	}

} ();

YAHOO.util.Event.addListener(window, 'load', YAHOO.smoothscroll.init);