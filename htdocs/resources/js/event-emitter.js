/**
* EventEmitter v4.0.3 - git.io/ee
* Oliver Caldwell
* MIT license
*/

// ********************
// EventEmitter
// ********************
(function (e) { "use strict"; function t() { } function i(e, t) { if (r) return t.indexOf(e); var n = t.length; while (n--) if (t[n] === e) return n; return -1 } var n = t.prototype, r = Array.prototype.indexOf ? !0 : !1; n.getListeners = function (e) { var t = this._events || (this._events = {}); return t[e] || (t[e] = []) }, n.addListener = function (e, t) { var n = this.getListeners(e); return i(t, n) === -1 && n.push(t), this }, n.on = n.addListener, n.removeListener = function (e, t) { var n = this.getListeners(e), r = i(t, n); return r !== -1 && (n.splice(r, 1), n.length === 0 && (this._events[e] = null)), this }, n.off = n.removeListener, n.addListeners = function (e, t) { return this.manipulateListeners(!1, e, t) }, n.removeListeners = function (e, t) { return this.manipulateListeners(!0, e, t) }, n.manipulateListeners = function (e, t, n) { var r, i, s = e ? this.removeListener : this.addListener, o = e ? this.removeListeners : this.addListeners; if (typeof t == "object") for (r in t) t.hasOwnProperty(r) && (i = t[r]) && (typeof i == "function" ? s.call(this, r, i) : o.call(this, r, i)); else { r = n.length; while (r--) s.call(this, t, n[r]) } return this }, n.removeEvent = function (e) { return e ? this._events[e] = null : this._events = null, this }, n.emitEvent = function (e, t) { var n = this.getListeners(e), r = n.length, i; while (r--) i = t ? n[r].apply(null, t) : n[r](), i === !0 && this.removeListener(e, n[r]); return this }, n.trigger = n.emitEvent, typeof define == "function" && define.amd ? define(function () { return t }) : e.EventEmitter = t })(this);