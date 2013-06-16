
// attach clear and restore on chronicles search form
toInit.push("attachContentSearchFormClearingAndRestore()");

/* attach clear and restore on content search form */
function attachContentSearchFormClearingAndRestore() {
    _attachInputClearingAndRestore("contentSearchTermDefaultLabel", "#contentSearchTerm");
}

