

/* ======================================================================= */
/* ====================== attaching functions ============================ */
/* ======================================================================= */


toInit.push("attachSearchBookNavigation()");
toInit.push("attachBookSearchEvents()");


/* Search book result navigation */
function attachSearchBookNavigation(){    
    $(".pageNav.searchBook a").click(function(event){
        _doNav(event, this, "#searchBookResults", 'book-search/get-page');
    });
}


/* Book search field clearing on focus */
function attachBookSearchEvents() {
    _attachInputClearingAndRestore("bookSearchTermDef", "#nav-main .bookSearchTermField");    
}