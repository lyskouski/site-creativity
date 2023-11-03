/**
 * PROXY polyfill
 */
if (typeof window.Proxy === 'undefined') {
    window.Proxy = function (data, func) {
        // Just in case
        func.__methodNotFound__ = func.get;
        // Use events
        for (var name in data) {
            func[name] = data[name];
        }
        return func;
    };
}