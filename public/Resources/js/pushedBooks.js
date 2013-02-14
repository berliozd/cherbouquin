

/* ======================================================================= */
/* ====================== attaching functions ============================ */
/* ======================================================================= */


toInit.push("attachAllExpandCollapse()");


function attachAllExpandCollapse() {
    
    _attachExpandCollapseBehavior("pushedBooks", "pushedBook", "Voir moins de livre", "Voir plus de livres");    
    
    _attachExpandCollapseBehavior("lastReviews", "lastReview", "Voir moins de critique", "Voir plus de critiques");
    
    _attachExpandCollapseBehavior("userEvents", "userEvent", "Voir moins d'activités", "Voir plus d'activités");

}

