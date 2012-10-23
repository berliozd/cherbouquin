$(function () {
    // Récupération des TR dont l'index est supérieur à 3
    var nbBooksShownByDefault = 3;
    var idx = nbBooksShownByDefault-1;
    var rows = jQuery(".pushedBook:gt(" + idx + ")", jQuery(".pushedBooks"));
    var link = jQuery(".lnkCollapseExpand", jQuery(rows).parents(".pushedBooks"));
    // Récupération des cellules visibles dans ces TR
    var visibleCells = jQuery("DIV:visible", rows);
    // si il y en a, on doitles cacher
    if (visibleCells.length > 0){
        updateList(rows, link, false);
    }else {// sinon on doit les afficher
        updateList(rows, link, true);
    }

    $(".lnkCollapseExpand").click(function(p_event){
        p_event.preventDefault();
        // Récupération des TR dont l'index est supérieur à 3 dans la liste parente
        var rows = jQuery(".pushedBook:gt(" + idx + ")", jQuery(this).parents(".pushedBooks"));
        var visibleCells = jQuery("DIV:visible", rows);
        if (visibleCells.length > 0){
            updateList(rows, this, false);
        }else {
            updateList(rows, this, true);
        }

    });

});

function updateList(rows, link, show){
    if (show){
        jQuery(rows).show();
        jQuery(link).text("Voir moins de livre");
    }else{
        jQuery(rows).hide();
        jQuery(link).text("Voir plus de livres");
    }
}