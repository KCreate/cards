(function () {
	console.log("Snowflake effect copied and changed from http://codepen.io/fsylum/pen/vqKfL?editors=001");
	var minWidth = 570;
	var initialResizeDelay = 3000;
	var resizeDelay = 500;
	var COUNT = 3e2;
	var hero = document.querySelector('#hero');

	// canvas configuration
	var canvas = document.createElement('canvas');
	canvas.style.pointerEvents = "none";
	var ctx = canvas.getContext('2d');

	var width = hero.clientWidth;
	var height = hero.clientHeight;
	var i = 0;
	var active = false;

	var firstResize = true;
	function onResize() {
		firstResize = false;
		width = hero.clientWidth;
		canvas.width = width;
		ctx.fillStyle = "#ecf0f1";

		var wasActive = active;
		active = width > minWidth;

		if (!wasActive && active) {
			requestAnimFrame(update);
		}

		setTimeout(function(){
			height = hero.clientHeight;
			canvas.height = height;
			ctx.fillStyle = "#ecf0f1";
		}, resizeDelay);
	}

	var Snowflake = function () {
		this.x = 0;
		this.y = 0;
		this.vy = 0;
		this.vx = 0;
		this.r = 0;
		this.reset();
	};

	// snow flake movement and size configuration
	Snowflake.prototype.reset = function() {
		this.x = Math.random() * width;
		this.y = Math.random() * -height;
		this.vy = Math.random()*0.8;
		this.vx = 0.3 - 0.6*Math.random();
		this.r = 1 + Math.random() * 3;
		this.o = 0.4 + Math.random() * 0.6;
	};

	canvas.style.position = 'absolute';
	canvas.style.opacity = '0.9';
	canvas.style.left = canvas.style.top = '0';

	var snowflakes = [], snowflake;
	for (i = 0; i < COUNT; i++) {
		snowflake = new Snowflake();
		snowflakes.push(snowflake);
	}

	function update() {

		ctx.clearRect(0, 0, width, height);

		if (!active)
		return;

		for (i = 0; i < COUNT; i++) {
			snowflake = snowflakes[i];
			snowflake.y += snowflake.vy;
			snowflake.x += snowflake.vx;

			ctx.globalAlpha = snowflake.o;
			ctx.beginPath();
			ctx.arc(snowflake.x, snowflake.y, snowflake.r, 0, Math.PI * 2, false);
			ctx.closePath();
			ctx.fill();

			if (snowflake.y > height) {
				snowflake.reset();
			}
		}

		requestAnimFrame(update);
	}

	// shim layer with setTimeout fallback
	window.requestAnimFrame = (function(){
		return  window.requestAnimationFrame       ||
		window.webkitRequestAnimationFrame ||
		window.mozRequestAnimationFrame    ||
		function( callback ){
			window.setTimeout(callback, 1000 / 60);
		};
	})();

	onResize();
	window.addEventListener('resize', onResize, false);

	hero.appendChild(canvas);
})();
