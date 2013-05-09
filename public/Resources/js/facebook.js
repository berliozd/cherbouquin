(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id))
        return;
    js = d.createElement(s);
    js.id = id;
    js.src = share1BookAjax.facebookJs;
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// http://developers.facebook.com/docs/reference/dialogs/requests/
function facebookNewInvite() {
    var receiverUserIds = FB.ui({
        method : 'apprequests',
        message : share1BookAjax.facebookInviteText
    });
}