Vaviorka.model.sample = (function () {
    /**
    * Sample model
    * @sample
    *    var alertHi = new Vaviorka.model.sample.hi();
    *    // 1. -> load file /js/classes/model/sample.js
    *    // 2. -> trigger hi-function
    *    // 3. -> if hi-function is missing, try to load file /js/classes/model/sample/hi.js
    *
    * @returns {unresolved}
    */
   var val = 0;

    var data = {
        setValue: function(i) {
            val = i;
        },
        hi: function() {
            alert('hi '+val+' !');
        }
    };

    return Vaviorka.model.bind(data, 'sample');

});