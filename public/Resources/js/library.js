

/* ======================================================================= */
/* ====================== attaching functions ============================ */
/* ======================================================================= */


toInit.push("attachLibraryNavigation()");
toInit.push("attachLibrarySorting()");
toInit.push("attachListEvents()");


/* Search book result navigation */
function attachLibraryNavigation(){
    $(".pageNav.library a").click(function(event){
        _doNav(event, this, ".library-list", 'library/get-page');
    });
}


/* Library sorting */
function attachLibrarySorting() {
    $(".library-list .sortLine .sort").click(function(event){
        _doSort(event, this, ".library-list", 'library/sort');
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