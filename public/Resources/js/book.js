

/* ======================================================================= */
/* ====================== attaching functions ============================ */
/* ======================================================================= */


toInit.push("initReadingDateDatePicker()");
toInit.push("attachReadingFieldOnChange()");
toInit.push("attachUserBookFormSubmit()");
toInit.push("attachStarRatingBehavior()");
toInit.push("attachBookReviewsNavigation()");
toInit.push("attachCommentBoxToggle()");
toInit.push("attachUserbookCommentPosting()");
toInit.push("attachOffensiveCommentWarning()");
toInit.push("attachBadDescriptionWarning()");

/* Init reading date Datepicker */
function initReadingDateDatePicker() {
    $('#readingDateDatepicker').datepicker({
        inline: true
    });
}


/* Hide or show the reading fields */
function attachReadingFieldOnChange(){
    $('#selReadingState').change(function(){
        if ($(this).val() == "3"){ // 3 = read state
            $('.readingDateBlock').show();
        }else{
            $('.readingDateBlock').hide();
        }
        
        if ($(this).val() == "2"){ // 2 = reading state
            $('.nbOfPagesReadBlock').show();
        }else{
            $('.nbOfPagesReadBlock').hide();
        }
    });
}


/* Test on textarea review if hyperlink not empty */
function attachUserBookFormSubmit(){
    var minLength = 140;
    $("#userBookFrm").submit(function() {
        
        // Testing the hyperlink and textarea
        var linkNotNull = ($("#hyperlink").val() != "");
        var reviewOk = ($("#review").val().length > minLength);
        if (linkNotNull && !reviewOk) {
            alert("Pour renseigner un lien, vous devez renseigner un commentaire d'une longueur supérieur ou égale à " + minLength + " caractères.");
            return false;
        }
        
        // Testing the number of pages
        var nbOfPages = $("#nbOfPages").val();
        var nbOfPagesRead = $("#nbOfPagesRead").val();
        if (isNaN(nbOfPages) || isNaN(nbOfPagesRead)) {
            alert("Caractères invalides dans les champs de saisie de nombre de pages");
            return false;
        }
        if (eval(nbOfPages) < eval(nbOfPagesRead)) {
            alert("Le nombre de pages lues ne peut être supérieur au nombre de pages du livre.");
            return false;
        }
    });
}


/* Behavior when mouse overing, mouse clicking stars */
function attachStarRatingBehavior() {
    $('.star').click(function(){
        rating = $(this).attr('rating');
        _changeStarsCssRating(rating);
        setHiddenRating(rating)
    });
    $('.star').mouseover(function(){
        rating = $(this).attr('rating');
        _changeStarsCssRating(rating);
    });
    $('.star').mouseout(function(){
        _revertStarsCssRating();
    });
}


/* Book reviews navigation */
function attachBookReviewsNavigation() {
    $(".bv-reviews .navigation .nav-links a").click(function(event){
        _doNav(event, this, ".bv-reviews", 'book/get-reviews-page');
    });
}


///* Show / Hide comment box*/
function attachCommentBoxToggle() {
    $(".commentLinkToggle").click(function(event) {
        event.preventDefault();
        _showAllComments(this);
    });    
  
    var defClass = "commentFormDefValue";
    var defValue = $("." + defClass).val();    
    $(".addUserbookCommentForm .comment").val(defValue);
    _attachInputClearingAndRestore(defClass, ".addUserbookCommentForm .comment");
}

/* post userbook comment */
function attachUserbookCommentPosting() {
    $(".addUserbookCommentForm").submit(function(event) {
        event.preventDefault(event);
        _addUserBookComment(this);
    });
}

/* warn offensive comment */
function attachOffensiveCommentWarning() {
    $(".js_warnOffensiveComment").click(function(event) {
        event.preventDefault(event);
        _warnOffensiveComment();
    });
}

/* warn bad description */
function attachBadDescriptionWarning() {
    $(".js_warnBadDescription").click(function(event) {
        event.preventDefault(event);
        _warnBadDescription();
    });
}

/* ======================================================================= */
/* ====================== private function =============================== */
/* ======================================================================= */


function _changeStarsCssRating(rating){
    $('#stars').removeClass();
    if (rating){
        $('#stars').addClass('rating-'+rating);
    }else {
        $('#stars').addClass('no-rating');
    }
}
function setHiddenRating(rating){
    if (rating){
        $('#hiddenRating').val(rating);
    }else {
        $('#hiddenRating').val('');
    }
}
function _revertStarsCssRating() {
    _changeStarsCssRating($('#hiddenRating').val());
}

function _showAllComments(sender) {
    var liSender = $(sender).parents("li");    
    var items = $("li", $(sender).parents(".bvr-comments"));    
    items.show();
    liSender.hide();
}

function _addUserBookComment(sender) {  
    if (_isUserbookCommentAdFormValid(sender)) {
        $("#loading #loadingMsg").html("En cours ..."); 
        $("#loading").show(); // Show loading message
        var seralizedData = _getUserbookCommentDataString(sender);
        $.ajax({  
            type: "POST",        
            url: share1BookAjax.url + "member/comments/add-userbook-comment/format/html",  
            data: seralizedData,
            success: function(data) {                
                $("#loading").hide(); 
                if (data.indexOf("errorMessage:") >= 0) {
                    var message = data.replace("errorMessage:", "");
                    _addFlashMessage(message);
                } else {
                    $(".bv-reviews").html(data);                
                    _addFlashMessage("Votre commentaire a été posté correctement.");
                    // Call function defined in ajax.js to init all event listners
                    ajaxInit();
                }
            }
        });
    }
}

function _isUserbookCommentAdFormValid(sender) {
    var comment = _getUserbookCommentVal(sender, "comment");
    var defClass = "commentFormDefValue";
    var defValue = $("." + defClass).val();
    
    var isValid = true;
    
    if ((comment.length == '' ) || (comment == defValue)) {
        alert('Vous devez renseigner un commentaire.');    
        isValid = false;
    }        
    if (comment.length > 5000 ) {
        alert('Votre commentaire est trop long. 5000 caractères maximum svp.');
        isValid = false;        
    }    
    if (comment.length <= 3 ) {
        alert('Votre commentaire est trop court. 4 caractères minimum svp.');
        isValid = false;
    }        
        
    return isValid;
}

function _getUserbookCommentDataString(sender) {
    var bookId = _getUserbookCommentVal(sender, "bookId");
    var reviewPageId = _getUserbookCommentVal(sender, "reviewPageId");
    var ubid = _getUserbookCommentVal(sender, "ubid");
    var comment = _getUserbookCommentVal(sender, "comment");
    return "bookId=" + bookId
    + "&reviewPageId=" + reviewPageId
    + "&ubid=" + ubid 
    + "&comment=" + comment;
}

function _getUserbookCommentVal(sender, classe){
    return $("." + classe, $(sender)).val();
}

function _warnOffensiveComment() {  
    $("#loading #loadingMsg").html("En cours ..."); 
    $("#loading").show(); // Show loading message
    $.ajax({  
        type: "POST",        
        url: share1BookAjax.url + "default/book/warn-offensive-comment/format/json",  
        data: "bid=" + $(".bv-reviews").attr('key'),
        success: function(data) {
            var message = eval(data).message;            
            _addFlashMessage(message);
        },
        error: function() {            
            _addFlashMessage("Erreur interne");
        }
    });
}

function _warnBadDescription() {  
    $("#loading #loadingMsg").html("En cours ..."); 
    $("#loading").show(); // Show loading message
    $.ajax({  
        type: "POST",        
        url: share1BookAjax.url + "default/book/warn-bad-description/format/json",  
        data: "bid=" + $(".book_Id").val(),
        success: function(data) {
            var message = eval(data).message;            
            _addFlashMessage(message);
        },
        error: function() {            
            _addFlashMessage("Erreur interne");
        }
    });
}