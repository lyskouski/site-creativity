/**
 * This function is called when someone finishes with the Login Button.
 * See the onlogin handler attached to it in the sample code below.
 */
function checkLoginState() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}

/**
 * This is called with the results from from FB.getLoginStatus()
 */
function statusChangeCallback(response) {
    var el = this;
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {    
        FB.api('/me', function (user) {
            var o = jQuery('#ui-fb')[0];
            o.accessToken.value = response.authResponse.accessToken;
            o.signed_request.value = response.authResponse.signedRequest;
            o.userID.value = response.authResponse.userID;
            o.name.value = user.name;
            jQuery('#ui-fb').removeAttr('style');
            jQuery('#ui-fb-button').remove();
        });
    }
}

window.fbAsyncInit = function () {
    FB.init({
        appId: '1567769353546721',
        xfbml: true,
        cookie: true,
        version: 'v2.5'
    });
    /**
     * These three cases are handled in the callback function.
     * 1. Logged into your app ('connected')
     * 2. Logged into Facebook, but not your app ('not_authorized')
     * 3. Not logged into Facebook and can't tell if they are logged into your app or not.
     */
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
};