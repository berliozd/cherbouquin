/* ======================================================================= */
/* ====================== global ajax loading ============================ */
/* ======================================================================= */

var toInit = new Array();
$(function() {
    ajaxInit();
});

function ajaxInit() {
    for ( var i = 0; i <= toInit.length; i++) {
        eval(toInit[i]);
    }
}

/* ======================================================================= */
/* ====================== attaching functions ============================ */
/* ======================================================================= */

toInit.push("attachFlashHiding()");
toInit.push("initProfileDatePicker()");
toInit.push("attachRegistrationFormEvents()");
toInit.push("attachFriendSearchEvents()");
toInit.push("attachLoginEvents()");
toInit.push("attachAddUserBook()");
toInit.push("attachPressReviewFormClearingAndRestore()");

/* Hide the flahes messages when clicking anywhere */
function attachFlashHiding() {
    $("#flashes-wrap").click(function() {
        $("#flashes-wrap").remove();
    });
}

/* Profile edit date picker */
function initProfileDatePicker() {
    // Datepicker
    $(".birthDateField").datepicker({
        inline : true,
        changeYear : true,
        changeMonth : true,
        yearRange : "c-100:c+100"
    });
}

/* Registration form events */
function attachRegistrationFormEvents() {
    $(".registrationPwd").focus(function() {
        $(this).val("");
    });
}

/* Friend search field clearing on focus */
function attachFriendSearchEvents() {
    _attachInputClearingAndRestore("friendSearchDef", ".friendSearchField");
}

/* Login field clearing on focus */
function attachLoginEvents() {
    _attachInputClearingAndRestore("loginDef", ".loginField");
    $(".loginPwd").focus(function() {
        $(this).val("");
    });
}

/* Add a userbook on click */
function attachAddUserBook() {
    $(".addUserBookBtn").click(function(p_event) {
        p_event.preventDefault();
        _addUserBook(this);
    });
}

/* Add clear and restore behavior on press review subscription form */
function attachPressReviewFormClearingAndRestore() {
    _attachInputClearingAndRestore("pressReviewFormEmailDefaultLabel", "#press-reviews-subscription-form #email");
}

/* ======================================================================= */
/* ====================== private function =============================== */
/* ======================================================================= */

// Fonction appelée lors des clicks sur les liens de navigation
function _doNav(event, sender, container, action) {
    var pagenumber = jQuery(sender).attr("pagenumber");
    _doAjax(event, container, action, pagenumber);
}

// Fonction appelée pour le tri
function _doSort(event, sender, container, action) {
    var sortCriteria = jQuery(sender).attr("sortcriteria");
    _doAjax(event, container, action, sortCriteria);
}

function _doAjax(event, container, action, paramValue) {
    var keyValue = $(container).attr("key");
    event.preventDefault();
    $("#loading #loadingMsg").html("Chargement en cours ...");
    $("#loading").show();

    /**
     * TEMPORARY : friendlibValue is used only for displaying library This won't
     * be usefull anymore when library will be moved to its proper Zend
     * controller For example library.isFriendLibrary is not available for
     * review pagination on book page or chronicle page
     */
    var friendlibValue = 0;
    if (typeof library != "undefined")
        friendlibValue = library.isFriendLibrary;

    jQuery.post(share1BookAjax.url + action + "/format/html", {
        param : paramValue,
        key : keyValue,
        friendlib : friendlibValue
    }, function(response) {
        $(container).html(response);
        $("#loading").hide();
        // Attache à nouveau le handler au click des liens de navigation dans le
        // code HTML renvoyé
        ajaxInit();
    });
};

function _getUserBookDataString(sender) {
    id = _getValue(sender, "book_Id");
    isbn10 = _getValue(sender, "book_ISBN10");
    isbn13 = _getValue(sender, "book_ISBN13");
    asin = _getValue(sender, "book_ASIN");
    title = _getValue(sender, "book_Title");
    description = _getValue(sender, "book_Description");
    imageUrl = _getValue(sender, "book_ImageUrl");
    smallImageUrl = _getValue(sender, "book_SmallImageUrl");
    largeImageUrl = _getValue(sender, "book_LargeImageUrl");
    author = _getValue(sender, "book_Author");
    publisher = _getValue(sender, "book_Publisher");
    publishingDate = _getValue(sender, "book_PublishingDate");
    amazonUrl = _getValue(sender, "book_AmazonUrl");
    nbOfPages = _getValue(sender, "book_NbOfPages");
    language = _getValue(sender, "book_Language");
    return "book_Id=" + id + "&book_ISBN10=" + isbn10 + "&book_ISBN13=" + isbn13 + "&book_ASIN=" + asin + "&book_Title=" + title
            + "&book_Description=" + description + "&book_ImageUrl=" + imageUrl + "&book_SmallImageUrl=" + smallImageUrl + "&book_LargeImageUrl="
            + largeImageUrl + "&book_Author=" + author + "&book_Publisher=" + publisher + "&book_PublishingDate=" + publishingDate
            + "&book_AmazonUrl=" + amazonUrl + "&book_NbOfPages=" + nbOfPages + "&book_Language=" + language;
};

function _getValue(sender, classe) {
    return escape($("." + classe, $(sender).parents(".book-data")).val());
}

function _addUserBook(sender) {
    // affiche le masque de loading "chargement en cours..."
    $("#loading #loadingMsg").html("Ajout en cours ...");
    // affiche le masque de loading "chargement en cours..."
    $("#loading").show();
    $.ajax({
        type : "POST",
        url : share1BookAjax.url + "userbook/add/format/html",
        data : _getUserBookDataString(sender),
        success : function(data) {
            // $('#flashes-wrap').remove();
            $("#loading").hide();
            _addFlashMessage(data);
        }
    });
}

function _attachInputClearingAndRestore(defClass, selector) {
    var bookSearchTermDef = $("." + defClass).val();
    $(selector).focus(function() {
        if ($(this).val() == bookSearchTermDef) {
            $(this).val("");
        }
    });

    $(selector).blur(function() {
        if ($(this).val() == "") {
            $(this).val(bookSearchTermDef);
        }
    });
}

function _addFlashMessage(message) {
    $("#loading").hide();
    $('#page').append(
            "<div id=\"flashes-wrap\"><div id=\"flashes-background\"></div><div id='flashes'><div id='flashes-close-button'></div><ul><li>" + message
                    + "</li></ul></div></div>");
    $("#flashes-wrap").click(function() {
        $("#flashes-wrap").remove();
    });
}

// Expand / Collapse ======================================
function _attachExpandCollapseBehavior(containerClass, itemClass, labelLess, labelMore) {
    // Récupération des items dont l'index est supérieur à 3
    var nbBooksShownByDefault = 3;
    var idx = nbBooksShownByDefault - 1;
    var rows = jQuery("." + itemClass + ":gt(" + idx + ")", jQuery("." + containerClass));
    var link = jQuery(".lnkCollapseExpand", jQuery(rows).parents("." + containerClass));

    // Récupération des cellules visibles dans ces TR
    var visibleCells = jQuery("DIV:visible", rows);
    // si il y en a, on doit les cacher
    if (visibleCells.length > 0) {
        _updateList(rows, link, false, labelLess, labelMore);
    } else {// sinon on doit les afficher
        _updateList(rows, link, true, labelLess, labelMore);
    }

    $("." + containerClass + " .lnkCollapseExpand").click(function(p_event) {
        p_event.preventDefault();
        // Récupération des TR dont l'index est supérieur à 3 dans la liste
        // parente
        var rows = jQuery("." + itemClass + ":gt(" + idx + ")", jQuery(this).parents("." + containerClass));
        var visibleCells = jQuery("DIV:visible", rows);
        if (visibleCells.length > 0) {
            _updateList(rows, this, false, labelLess, labelMore);
        } else {
            _updateList(rows, this, true, labelLess, labelMore);
        }

    });
}

function _updateList(rows, link, show, labelLess, labelMore) {
    if (show) {
        jQuery(rows).show();
        jQuery(link).text(labelLess);
    } else {
        jQuery(rows).hide();
        jQuery(link).text(labelMore);
    }
}

function initCarousel(className, width, height) {
    $("ul." + className).simplecarousel({
        width : width,
        height : height,
        auto : 8000,
        fade : 200,
        pagination : true
    });
}

function initCoverFlip(cssId, separation) {
    $("#" + cssId).waterwheelCarousel({
        startingWaveSeparation : 0,
        centerOffset : 30,
        startingItemSeparation : separation,
        itemSeparationFactor : .7,
        opacityDecreaseFactor : 1,
        autoPlay : 1500,
        movedToCenter : function(newCenterItem) {
            $(".coverflip-caption", $("#" + cssId)).hide();
            $(".coverflip-caption", newCenterItem.parent()).show();
        },
        clickedCenter : function(clickedItem) {
            document.location.href = clickedItem.attr('href');
        }
    });
}