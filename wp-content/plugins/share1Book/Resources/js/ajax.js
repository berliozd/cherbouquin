$(function () {
    attachShare1BookEvents();
});

function attachShare1BookEvents(){

    /* Hide the flahes messages when clicking anywhere */
    attachFlashHiding();

    /* Search book result navigation */
    attachSearchBookNavigation();

    /* Library navigation */
    attachLibraryNavigation();

    /* Library sorting */
    attachLibrarySorting();

    /* Filtering by title or author */
    attachListEvents();
    
    /* Book search field clearing on focus */
    attachBookSearchEvents();
    
    /* Friend search field clearing on focus */
    attachFriendSearchEvents();
    
    /* Login field clearing on focus */
    attachLoginEvents();
    
    /* Registration form events */
    attachRegistrationFormEvents();
    
    /* Profile edit date picker */
    initProfileDatePicker();
    
    /* Add a userbook on click */
    attachAddUserBook();        
       
}

/* Library sorting */
function attachLibrarySorting() {
    $(".library-list .sortLine .sort").click(function(event){
        doSort(event, this, ".library-list", "sorting", share1BookAjax.sortingNonce);
    });
}
    
/* Search book result navigation */
function attachLibraryNavigation(){
    $(".pageNav.library a").click(function(event){
        doNav(event, this, ".library-list", 'navigation', share1BookAjax.navigationNonce);
    });
}
    
/* Search book result navigation */
function attachSearchBookNavigation(){    
    $(".pageNav.searchBook a").click(function(event){
        doNav(event, this, "#searchBookResults", 'searchBookNagivation', share1BookAjax.searchBookNavigationNonce);
    });
}

/* Hide the flahes messages when clicking anywhere*/
function attachFlashHiding() {
    
    $("#flashes-wrap").click(function() {
        $("#flashes-wrap").remove();
    });
}

/* Profile edit date picker */
function initProfileDatePicker() {
    // Datepicker
    $(".birthDateField").datepicker({
        inline: true,
        changeYear: true,
        changeMonth: true,
        yearRange: "c-100:c+100" 
    });
}

/* Registration form events */
function attachRegistrationFormEvents() {
    $(".registrationPwd").focus(function() {        
        $(this).val("");        
    });
}

/* Book search field clearing on focus */
function attachBookSearchEvents() {
    attachInputClearingAndRestore("bookSearchTermDef", "#nav-main .bookSearchTermField");    
}

/* Friend search field clearing on focus */
function attachFriendSearchEvents() {
    attachInputClearingAndRestore("friendSearchDef", ".friendSearchField");    
}

/* Login field clearing on focus */
function attachLoginEvents() {
    attachInputClearingAndRestore("loginDef", ".loginField");    
    $(".loginPwd").focus(function() {        
        $(this).val("");        
    });
}

function attachInputClearingAndRestore(defClass, selector) {
    var bookSearchTermDef = $("." + defClass).val();
    $(selector).focus(function() {
        if ($(this).val() == bookSearchTermDef) {
            $(this).val("");
        }
    });

    $(selector).blur(function(){
        if ($(this).val() == "") {
            $(this).val(bookSearchTermDef);
        }
    });
}

/* Filtering by title or author */
function attachListEvents() {
    $("#authorsFirstLetterSelect").change(function(){
        val = $(this).val(); 
        $("#listFilterFormFilter").val(val);
        $("#listFilterFormFilterType").val("author");
        $("#listFilterForm").submit();
    });
    $("#titlesFirstLetterSelect").change(function(){
        val = $(this).val(); 
        $("#listFilterFormFilter").val(val);
        $("#listFilterFormFilterType").val("title");
        $("#listFilterForm").submit();
    });
}

// Fonction appelée lors des clicks sur les liens de navigation
function doNav(event, sender, container, action, nonce){
    var pagenumber = jQuery(sender).attr("pagenumber");
    doAjax(event, container, action, pagenumber , nonce);
}
// Fonction appelée pour le tri
function doSort(event, sender, container, action, nonce){
    var sortCriteria = jQuery(sender).attr("sortcriteria");
    doAjax(event, container, action, sortCriteria, nonce);
}
// Fonction appelée pour le search
function doSearch(event, container, action, nonce){
    var searchValue = jQuery("#listSearchValue").val();
    doAjax(event, container, action, searchValue, nonce);
}

function doAjax(event, container, action, paramValue, nonce){
    var key = $(container).attr("key");
    event.preventDefault();
    $("#loading #loadingMsg").html("Chargement en cours ...");
    $("#loading").show();
    jQuery.post(
        share1BookAjax.url,
        {
            action : action,
            param : paramValue,
            nonce : nonce,
            key : key,
            isfriendlibrary : library.isFriendLibrary
        },
        function( response ) {
            $(container).html(response);
            $("#loading").hide();
            // Attache à nouveau le handler au click des liens de navigation dans le code HTML renvoyé
            attachShare1BookEvents();
        }
        );

};

