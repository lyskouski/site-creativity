/**
 * Change ajax request with changes in href bar
 *
 * @name View/Animate/Loading
 */
window.Vaviorka.registry.include((function (jQuery) {
    /**
     * Internal functionality
     * @type object
     */
    var self = {
        oRepeat: null
        , oInitial: null
        , iCount: 0
        , iTimer: 0
        , bTimer: false
        , aTarget: []

        , show: function() {
            self.iTimer += self.bTimer ? 1 : -1;
            if(self.iTimer > 8) {
                self.iTimer = 7;
                self.bTimer = false;
            } else if (self.iTimer < 0) {
                self.iTimer = 1;
                self.bTimer = true;
            }
            var sImg = '';
            switch(self.iTimer) {
                case 0: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAlQTFRF////zMzMAAAACAUj9gAAAAN0Uk5T//8A18oNQQAAAK5JREFUeNqsk9EahSAIgzff/6FPimeMyuwiL8pPfkBwoGkBJFv/EHaqDcdq8UsExZyAEFQ7bUsD7JCnEBi3sxQeDAGgw/Iqt8EAwqCkM2OQHcB09eIV8jBfCq91R/s8/AkAeA8ksbCrN28BrIG2WXtgGQgfV3HNoU7i+S24fyzJGa51+RXB+AhM4i85Zj8Ayx2SS/0U0U6NPcwF3s9FGT3cjl4GWQ2vkBkhT38CDACv9gWqvvChUgAAAABJRU5ErkJggg=='; break;
                case 1: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAA9QTFRF////zMzMMzMzAAAAAAAASNC4vwAAAAV0Uk5T/////wD7tg5TAAAAt0lEQVR42qyTSwLEIAhDE/X+Zx5F5dPiOIthZclrREE0DYBkQy2FcFldUKKxjih4AEtWwBBE3QGFDqABqNECUl22BUdegC5iewhapi6fA5iC/L6CowDMzHbvhD/8tuyymYeAFq2rHICVfyIOut7NrwDOQLvEHTga4c+neO+hN4nvveC9WTrOyNrNMDD+CSxijxztPgC39xy5ZoUYqTP2ehe2RP4umALuXSDq1s2HA5J2ewfLfgQYAPq4CehYHe1fAAAAAElFTkSuQmCC'; break;
                case 2: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABVQTFRF////zMzMmZmZZmZmMzMzAAAAAAAAEJAmgwAAAAd0Uk5T////////ABpLA0YAAADvSURBVHjahJPRFsMgCEND6Pr/nzwRRLR246mnXCJCxJ0BiMgtn0uJ8jc/ROiAhcoGWHGPGx8PRQUsz6rQ4pIFYK9nBZAAEB2wKDQB0oGWxB0tGEr1vKqyAyMheSO5rL4BaoCrN416eVh9lwhx1rQFHVAbX9zvDBDZ3QsBP2AXaDGBPqNfwKHFcsYhs8Z/4OUSMxN7fgKRGHM4Aa4Axi4eJ/QEMJyAQwdWWJzwFOgejU/Yiuu6hwvSMFVkemxYTuY8kMN3j6VpGUvzFyDpscX2NjCML8H2cKYC64bXp0cZA8qWH4933x72549tal8BBgD8dQ7Jp8qujgAAAABJRU5ErkJggg=='; break;
                case 3: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABVQTFRF////zMzMmZmZZmZmMzMzAAAAAAAAEJAmgwAAAAd0Uk5T////////ABpLA0YAAADoSURBVHjahJNZEsQgCESbJvH+Rx4RREyZDF+p8GhW0dIAEWlyX0qUv/khQgfMVB6ABQ9ruN0UFTA/q0K3SzaAI54VQAJAVMCi0AVIB7oTLUowlOp+VeUApkOyI7ksvgNqgKt3jdo8LH5IhDir24wOqI0v+jsDRFb3QsATPAW6LWDM6As4lFhyHDy7/QdemlgpYs/fRX52AcYuzhmIeQl4G3U7z3otK84NtuLzuuNgqsg4Hp0HM0ax5oH84jw5P1rG0vIFdA0+z94GBspW9no4qcCt8f3pcaYQAi+PN7LhsG5kO3UkPwEGAJy9D0ITCSgHAAAAAElFTkSuQmCC'; break;
                case 4: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABVQTFRF////zMzMmZmZZmZmMzMzAAAAAAAAEJAmgwAAAAd0Uk5T////////ABpLA0YAAADlSURBVHjahJNREsQgCENDaL3/kVcEFR3t8tVpngEBUUYAIlLkfZRIf8eHCB2wUNkAO9yi4PVQZMB0ZocajywA23lmAAMAogImh2pAOlBFlCjBUKrrqsoGdEHGjeSx8xVQA9y9euTLw843izBnli3ogFr74n5ngBjVXQh4gt2gxgRaj76AQ4kpx0FZ4z9wucRMEXP+LvLzFmDM4pyB6JuAW6vLuddzWLFusBFPmWncsTDZhKZoX5jWitkP9uocm0vLGFpPbh7c154ZaHJ+OLvDAvQSuKQ4P14u8jJuf/4GkPPvT4ABAOCsD8j1eUGTAAAAAElFTkSuQmCC'; break;
                case 5: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABVQTFRF////zMzMmZmZZmZmMzMzAAAAAAAAEJAmgwAAAAd0Uk5T////////ABpLA0YAAADeSURBVHjajJLbEsMgCESXJfX/P7kiiJgxaXly5LBc0dIAEWnyuZQov/kQoQNmKjfAgoc1fNwUFTA/q0K3SzaAI54VQAJAVMCi0AVIB7oTLUowlOp+VeUApkOyI7ksvgNqgKt3jdo8LH5IhDir24wOqI0v+jsDRFb3QMAT3AW6LWDM6A04lFhyHDy7/QYemlgpYs/vRb52AcYuzhmIeQl4GnU7z3otK84Nfj/TzbLuOBi/nwwPYp6cZE09OF/z5Nb9WMhMbhrMUfMARMY/gfVdU+zb5A7wsG4mUEfyFWAAJJ4QQ3AvTmgAAAAASUVORK5CYII='; break;
                case 6: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABVQTFRF////zMzMmZmZZmZmMzMzAAAAAAAAEJAmgwAAAAd0Uk5T////////ABpLA0YAAADZSURBVHjajJJREsQgCEND6Hr/Iy+IIu1od/li5JkAipYBiEiTz6VEOc1EhAF4qDwAv9yj4ROhqIDXWRUsLrkB7PdZASQAjA5YFEyADMCKaKMFR6lRV1V2YBYkJ5LL7xugDoS6adTh4fe7xBBnLXswAPX1jfn2AJHdHQiEwVPAYgF9R2/ApsXisanc4zcwF3K0mAs5NvkvwIMDwT2xVt3eARuTo86CWL6eu0VSRTgO4sN0iWVEZja/nAtOS12ZaTBXnacFGI6oSzkDe4v7a/IOcPPcTKCu5CvAANFyEL5UQYS0AAAAAElFTkSuQmCC'; break;
                case 7: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABVQTFRF////zMzMmZmZZmZmMzMzAAAAAAAAEJAmgwAAAAd0Uk5T////////ABpLA0YAAADNSURBVHjarJJRDsMwCEONabn/kRcoI3RLFU2avxB5mAQCKwEiYnIeSrRsBSK8AJfKB+DFIcN5SdEBP2d3GDrkBjDq2QEUQOYN2ByGwcgHQFVaXgFupte5et4BjQh+mpLD6z3vQATu0R+PKIvCjNyti5k2VLQGiLXBJLA2MLMfAX4D2QO20R7YtvjXKx4HxRo1n0a9XVatmw0Z8Vx3fphuwkxcHyYsZiOyoveXc8N3S53R8GCNurINyI7oQ3kG1i3u2+Qd4GLdLKCP5CXAAB+jETVHHCV5AAAAAElFTkSuQmCC'; break;
                case 8: sImg = 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABVQTFRF////zMzMmZmZZmZmMzMzAAAAAAAAEJAmgwAAAAd0Uk5T////////ABpLA0YAAADBSURBVHjarJNLEsQgCEShSbz/kQdRgTiknMWwSBF5NsVHam6AiDS+LwHFqbs9OoBuwhswwwrQPUwoAx5fCmoXJ0AqgBwAUgoOAT03QINYGmjEkBG33w6MgNU4ja9+3wQVwLxqgqs2aq4Y4g+Dl+1eDYBqgSCoFoje/ArgG5g5qB3sDBxT/KuK10bBW423Vh+HVY4bSOOeC5NFYsfWykkkAtxbK/dcWvdUA9W7CBf1u6iBOkX9eLG1hbbe2De35CPAANivEa+i0Ty+AAAAAElFTkSuQmCC'; break;
            }

            var oIcon = jQuery('link[rel="shortcut icon"]').attr('href', 'data:image/png;base64,'+sImg);
            self.update( sImg );
            oIcon.remove();
            jQuery('head').append(oIcon);
            // jQuery('*').a('cursor', 'wait');
        }

        , update: function( sBgImage ) {
            for (var i = 0; i < self.aTarget.length; i++) {
                if (sBgImage) {
                    if (self.aTarget[i].height() > 100) {
                        self.aTarget[i].css('background', 'url("data:image/gif;base64,' + sBgImage + '") no-repeat 4px ' + self.aTarget[i].css('background-color'));
                        self.aTarget[i].css('background-size', 'auto auto');
                        self.aTarget[i].css('background-position', 'left top');
                    } else {
                        self.aTarget[i].css('background', 'url("data:image/gif;base64,' + sBgImage + '") no-repeat 4px center ' + self.aTarget[i].css('background-color'));
                        self.aTarget[i].css('background-size', 'auto 56%');
                    }
                    self.aTarget[i].css('padding-left', '24px');
                } else {
                    self.aTarget[i].removeAttr('style');
                    self.aTarget[i].removeClass('el_hidden');
                }
            }
        }
    };

    /**
     * External functionality
     * @type object
     */
    return {
        getName: function() {
            return 'View/Animate/Loading';
        }

        , add: function( obj ) {
            self.aTarget.push( obj );
        }

        , start: function( obj ) {
            if (!self.iCount) {
                self.oInitial = jQuery('link[rel="shortcut icon"]').clone();
            }
            if (typeof obj !== 'undefined') {
                self.aTarget.push( obj );
            }
            self.oRepeat = setInterval(self.show, 150);
            self.iCount++;

        }

        , stop: function() {
            self.iCount--;
            //if (!self.iCount) {
                clearInterval(self.oRepeat);
                jQuery('link[rel="shortcut icon"]').remove();
                jQuery('head').append(self.oInitial);
                // jQuery('*').css('cursor', '');
                self.update('');
                self.aTarget = [];
            //} else
            if (self.iCount < 0) {
                self.iCount = 0;
            }
        }
    };

})( window.Vaviorka.query ));