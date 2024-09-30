/*___                    ___      _        _
 / _ \ _  _ ___ _ _ _  _/ __| ___| |___ __| |_ ___ _ _
| (_) | || / -_) '_| || \__ \/ -_) / -_) _|  _/ _ \ '_|
 \__\_\\_,_\___|_|  \_, |___/\___|_\___\__|\__\___/_|
                    |__/*/
function _qs(selector, return_value, _elFinder) {
    var _qs;

    if (_elFinder && _elFinder != '') {
        _qs = _elFinder.querySelector(selector);
    } else {
        _qs = document.querySelector(selector);
    }

    if (_qs) { // && _qs != ''
        if (return_value) {
            return _qs.value;
        } else {
            return _qs;
        }
    } else {
        return null;
    }
}
HTMLSelectElement.prototype._qs = function (selector, return_value) {
    return _qs(selector, return_value, this);
};

HTMLElement.prototype._qs = function (selector, return_value) {
    return _qs(selector, return_value, this);
};

function _qsa(selector, _elFinder) {
    var _qsa;

    if (_elFinder && _elFinder != '') {
        _qsa = _elFinder.querySelectorAll(selector);
    } else {
        _qsa = document.querySelectorAll(selector);
    }

    if (_qsa && _qsa != '') {
        return _qsa;
    } else {
        return null;
    }
}
HTMLSelectElement.prototype._qsa = function (selector, return_value) {
    return _qsa(selector, this);
};

HTMLElement.prototype._qsa = function (selector, return_value) {
    return _qsa(selector, this);
};