// attach clear and restore on chronicles search form
toInit.push("attachContentSearchFormClearingAndRestore()");
// attach clear and restore on press review subscription form
toInit.push("attachPressReviewFormClearingAndRestore()");

/* attach clear and restore on content search form */
function attachContentSearchFormClearingAndRestore() {
    _attachInputClearingAndRestore("contentSearchTermDefaultLabel", "#contentSearchTerm");
}

/* Add clear and restore behavior on press review subscription form */
function attachPressReviewFormClearingAndRestore() {
    _attachInputClearingAndRestore("pressReviewFormEmailDefaultLabel", "#press-reviews-subscription-form #email");
}
