var rand, str_contains, wiggle;

rand = function(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
};

str_contains = function(haystack, needle) {
  return haystack.indexOf(needle > -1);
};

jQuery.extend({
  bez: function(encodedFuncName, coOrdArray) {
    var polyBez;
    polyBez = void 0;
    if (jQuery.isArray(encodedFuncName)) {
      coOrdArray = encodedFuncName;
      encodedFuncName = 'bez_' + coOrdArray.join('_').replace(/\./g, 'p');
    }
    if (typeof jQuery.easing[encodedFuncName] !== 'function') {
      polyBez = function(p1, p2) {
        var A, B, C, bezCoOrd, xDeriv, xForT;
        A = void 0;
        B = void 0;
        C = void 0;
        bezCoOrd = void 0;
        xDeriv = void 0;
        xForT = void 0;
        A = [null, null];
        B = [null, null];
        C = [null, null];
        bezCoOrd = function(t, ax) {
          C[ax] = 3 * p1[ax];
          B[ax] = 3 * (p2[ax] - p1[ax]) - C[ax];
          A[ax] = 1 - C[ax] - B[ax];
          return t * (C[ax] + t * (B[ax] + t * A[ax]));
        };
        xDeriv = function(t) {
          return C[0] + t * (2 * B[0] + 3 * A[0] * t);
        };
        xForT = function(t) {
          var i, x, z;
          i = void 0;
          x = void 0;
          z = void 0;
          x = t;
          i = 0;
          z = void 0;
          while (++i < 14) {
            z = bezCoOrd(x, 0) - t;
            if (Math.abs(z) < 0.001) {
              break;
            }
            x -= z / xDeriv(x);
          }
          return x;
        };
        return function(t) {
          return bezCoOrd(xForT(t), 1);
        };
      };
      jQuery.easing[encodedFuncName] = function(x, t, b, c, d) {
        return c * polyBez([coOrdArray[0], coOrdArray[1]], [coOrdArray[2], coOrdArray[3]])(t / d) + b;
      };
    }
    return encodedFuncName;
  }
});

wiggle = function() {
  alert("Are you ready?");
  $("head").html($("head").html() + '<style>@keyframes wiggle{0%{transform:rotate(0.5deg) scale(1.02) rotateX(25deg) rotateY(-30deg) skewX(5deg) skewY(10deg)}50%{transform:rotate(-0.5deg) scale(0.98) rotateX(-25deg) rotateY(-30deg) skewX(-5deg) skewY(-10deg)}100%{transform:rotate(0.5deg) scale(1.02) rotateX(25deg) rotateY(-30deg) skewX(5deg) skewY(10deg)}}#content div div{animation:wiggle calc(1s/2) infinite}</style>');
};
