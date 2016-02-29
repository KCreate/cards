$(document).ready(function() {
	var content = $('#content>div');
	// get an array of required sites
	var reqSites = [];

	// caching init
	var acache = cache();
	$("#hero>ul>li").each(function(index, item) {
		reqSites.push(item.firstChild.id);
	});
	//current url
	var url = window.location.href;
	var mode = url.split('#')[1];
	if ($.inArray(mode, reqSites) == -1 || mode === undefined) {
		mode = reqSites[0];
		window.history.replaceState('-', '-', "#"+mode);
	}
	var inProgress = true;
	var writeAnimationString = "Leonard Schuetz";
	var herotitle = $("#hero>div>h1");
	var navbar = $("#hero>ul");
	var hero = $('#hero');

	$('#'+mode).addClass('current');
	$("#hero>ul>li").click(function(event) {
		event.preventDefault();
		if (!inProgress) {
			inProgress = true;
			var newID = event.target.id;
			navigateToSection(newID,function() {
				inProgress = false;
			});
		}
    });

	$(window).on('hashchange', function(){
		var newhash = window.location.hash.split("#")[1];
		if (!inProgress) {
			inProgress = true;
			navigateToSection(newhash,function() {
				inProgress = false;
			});
		}
	});

	// caching
	section(reqSites.join("*"));
	writeAnimation();

	// get a section either from the backend or the cache
	function section(source, callback, cache) {
		// default values for the arguments
		callback = (typeof callback === 'undefined') ? function(){} : callback;
		cache = (typeof cache === 'undefined') ? true : cache;

		source = source.split("*");
		var length = source.length;
		var answer = [];

		source.forEach(function(i,index) {
            if (!answer[i]) { answer[i]="";}

			// check the cache
			var cachedOrInjected = false;
            var hasRemoteSource = false;
            if (acache.get(i)!==undefined) {
				answer[i] += acache.get(i).data;
                cachedOrInjected = true;
            }
			if ($("#JSINJECT-DATA-"+i).length!==0) {
				// check JSINJECT-DATA
                hasRemoteSource = $("#JSINJECT-DATA-"+i)[0].hasAttribute('hasRemoteSource');
				var jsinject = $("#JSINJECT-DATA-"+i).remove();
				acache.append(i, jsinject.html().trim());
				answer[i] += jsinject.html().trim();
                cachedOrInjected = true;
			}
            if (!cachedOrInjected || hasRemoteSource) {
				// request from the server
				var protocol = window.location.protocol;
				var hostname = window.location.hostname;
				var url = protocol+"//"+hostname+"/app/php/serve.php?show="+i;

				var received = false;
				$.get(url, function(response) {
					received = true;
					acache.append(i, response);
					answer[i] += response;
				});

				// timeout handler, delay of 3 seconds allowed
				setTimeout(function() {
					// check if the answer was received
					if (!received) {
						acache.append(i, "timeout", function() {
							if (i==mode) {
								navigateToSection(i, undefined, {force:true});
							}
						});
						answer[i] = "timeout";
						callback(answer);
						return;
					}
				}, 3000);
			}

			// if this is the last iteration of forEach,
			// call the callback with the answer
			if (index==length-1) {
				callback(answer);
			}
		});
	}

	function prepareForWriteAnimation() {
		//Reset
		content.css("display", "none");
		navbar.css("opacity", "0");
		hero.css("height", "100vh");

		//Get the hero title
		if (herotitle.text() === "") {
			herotitle.text("");
		} else {
			writeAnimationString = herotitle.text().trim();
			herotitle.text("");
		}

		// center the title
		herotitle.css({
			"position":	"absolute",
			"left":		"0",
			"top":		"50%",
			"width":	"100vw",
			"margin":	"0",
			"opacity":	"1"
		});
	}

	function writeAnimation() {
		prepareForWriteAnimation();

		//Write in the name
		//var string = writeAnimationString+"¬";
		var string = writeAnimationString;
		var characters = string.split("");
		var time = 600;
		var i = 0;
		//Show the rest of the nodes
		characters.forEach(function(character) {
			setTimeout(function(index) {
				herotitle.text(herotitle.text() + character);

				//reset
				if (index == (characters.length-1)) {
					setTimeout(function(index) {
						resetWriteAnimation();
					}, 75);
				}
			}, time, i);
			i += 1;
			time += rand(40, 60);
		});
	}

	function resetWriteAnimation() {
		//center the hero title on the page
		herotitle.css("position", "");
		var end = herotitle.position();
		herotitle.css("position", "absolute");

		herotitle.animate({
			top: end.top
		}, 700, $.bez([0.55,0,0.1,1]), function() {
			//Reset
			herotitle.removeAttr("style");
		});

		// header
		setTimeout(function() {
			hero.removeAttr("style");

			// content
			setTimeout(function() {
				content.removeAttr("style");
				navigateToSection(mode, function(){
					inProgress = false;
				}, {force:true});
			}, 130);

			// navbar, should be higher that before
			setTimeout(function() {
				navbar.removeAttr("style");
				inProgress = false;
			}, 500);
		}, 200);
	}

	function navigateToSection(event, callback, attributes) {
		// default value for attributes
		attributes = (attributes) ? attributes : {};

		var scrollTop = $(document).scrollTop();
		var targetid = event;

		// don't do anything if the current page isn't inside reqSites
		if ($.inArray(targetid, reqSites) == -1 || targetid == mode && !attributes.force) {
			if (callback!==undefined) {
				callback();
			}
			return;
		}

		//Scroll to the top and replace the content
		if ($(document).scrollTop() > 0) {
			$("body").animate({
				scrollTop: 0
			}, 200, function() {
				changeSection(targetid, function() {
					if (callback!==undefined) {
						callback(true);
					}
				});
			});
		} else {
			changeSection(targetid, function() {
				if (callback!==undefined) {
					callback(true);
				}
			});
		}

		//Change current content
		$('#'+mode).removeClass('current');
		mode = targetid;
		$('#'+targetid).addClass('current');
		window.history.replaceState('-', '-', "#"+targetid);
	}

	function changeSection(_targetid, callback) {
		var card = $("#content>div>div");
		card.removeAttr('style');
		card.css({
			"opacity":			"0",
			"transform":		"scale(0.97) translateY(5px)"
		});

		setTimeout(function() {
			section(_targetid, function(response) {
				response = response[_targetid];

				// card that gets shown when a timeout happens
				var subtitle = "Loading";
				var	text = "Your internet connection doesn\'t seem to be the best. Wait a couple seconds until everything is loaded.";
				if (response=="timeout") {
					response = ['<div><h1 borderleft><span class="subtitle_lower">'+subtitle+'</span></h1><p>'+text+'</p></div>'];
				}

				// return if there are no domnodes to show
				var domnodes = response;
				if ($(domnodes).length === 0) {
					return;
				}

				// remove the style attributes from each card
				content.html('');
				content.removeAttr('style');

				// increment is the delay between each card animation
				var time = 0, increment = 60;
				var i = 0;
				// animate in each card
				$(domnodes).each(function(i, newnode) {

					// this is used for identification in the animation process
					var oldID = newnode.id;
					$(newnode).attr('id', i);
					var index=i;

					// delay between each card
					setTimeout(function(i) {
						$(newnode).css({
							"transform-origin":	"top right",
							"transform":		"scale(0.97) translateY(20px)",
							"opacity":			"0"
						});

						// hide the content of each node
						hideContent(newnode);

						// append it to the dom
						content.append(newnode);

						// without this delay css3 doesn't trigger the transition
						setTimeout(function(){
							$(newnode).removeAttr('style');

							// reset the styles applied by hideContent()
							$(newnode).find("*").css({
								"opacity":		"",
								"transform":	""
							});
						}, 100);

						// remove the index id on the element
						if (oldID!=="") {
							$(newnode).attr('id', oldID);
						} else {
							$(newnode).removeAttr('id');
						}

						//Callback after last item
						if (index == $(domnodes).length-1) {
							callback(true);
						}
					}, time, i);
					i += 1;
					time += increment;
				});
			});
		}, 300);

		function hideContent(node){
			$(node).find("*").css({
				"opacity":		"0.3",
				"transform":	"scale(0.98)"
			});
		}
	}

	// hero paralax animation
	$(window).on("scroll", function() {
		if ($(window).width() > 570) {
			var pageOffset = window.pageYOffset;
			var heroHeight = hero.height()+parseInt(hero.css("padding-top"));
			var animRange = heroHeight;
			var animParalax = -50;
			var offset = animRange - pageOffset;
			var translate_offset = ((offset / animRange) - 1) * animParalax;

			if (offset+translate_offset > 0) {
				hero.css({
					"transform": "translateY("+translate_offset+"px)"
				});
			}
		}
	});

	// remove the noscript tag since js is obviously on
	// #critical is the critical css style tag
	var blacklist = "noscript, #critical, script";
	$(blacklist).remove();

	// funny message
	console.log("You like to see how stuff works under the hood right? Feel free to ask me anything about how this site works internally ;)");
});
