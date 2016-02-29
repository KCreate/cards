var cache;

cache = function() {
  return {
    _data: {},
    _notifiers: {},
    get: function(key) {
      return this._data[key];
    },
    set: function(key, value, callback) {
      var oldValue;
      if (this._data[key] !== void 0) {
        if (this._data[key].data !== value) {
          oldValue = this._data[key].data;
          this._data[key].data = value;
          this._data[key].timestamp = Date.now();
        } else {
          return false;
        }
      } else {
        this._data[key] = {
          data: value,
          timestamp: Date.now()
        };
      }
      if (this._notifiers[key] !== void 0) {
        this._notifiers[key].callback(oldValue, value);
      }
      if (typeof callback === 'function') {
        this.setNotifier(key, callback);
      }
      return this._data[key];
    },
    append: function(key, value) {
      var oldValue;
      if (this._data[key] !== undefined) {
        if (this._data[key].data !== value) {
          oldValue = this._data[key].data;
          this._data[key].data += value;
          this._data[key].timestamp = Date.now();
        } else {
          return false;
        }
      } else {
        this._data[key] = {
          data: value,
          timestamp: Date.now()
        };
      }
      if (this._notifiers[key] !== void 0) {
        this._notifiers[key].callback(oldValue, oldValue+value);
      }
      if (typeof callback === 'function') {
        this.setNotifier(key, callback);
      }
      return this._data[key];
    },
    length: function() {
      return this._data.length();
    },
    getJSON: function(key) {
      if (key === void 0) {
        return JSON.stringify(this._data, null, 2);
      } else {
        return JSON.stringify(this._data[key], null, 2);
      }
    },
    setNotifier: function(key, callback) {
      this._notifiers[key] = {};
      this._notifiers[key].key = key;
      this._notifiers[key].callback = callback;
    }
  };
};
