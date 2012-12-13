$(function(){

    /* Init reading date Datepicker */
    initReadingDateDatePicker();

    /* Hide / show date picker */
    attachReadingFieldOnChange();
    
    /* Behavior when mouse overing, mouse clicking stars */
    attachStarRatingBehavior();

    /* Test on textarea review if hyperlink not empty */
    attachUserBookFormSubmit();
    
});

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
    });
}
    
/* Test on textarea review if hyperlink not empty */
function attachUserBookFormSubmit(){
    var minLength = 140;
    $("#userBookFrm").submit(function() {
        var linkNotNull = ($("#hyperlink").val() != "");
        var reviewOk = ($("#review").val().length > minLength);
        if (linkNotNull && !reviewOk) {
            alert("Pour renseigner un lien, vous devez renseigner un commentaire d'une longueur supérieur ou égale à " + minLength + " caractères.");
            return false;
        }            
    });
}

/* Behavior when mouse overing, mouse clicking stars */
function attachStarRatingBehavior() {
    $('.star').click(function(){
        rating = $(this).attr('rating');
        changeStarsCssRating(rating);
        setHiddenRating(rating)
    });
    $('.star').mouseover(function(){
        rating = $(this).attr('rating');
        changeStarsCssRating(rating);
    });
    $('.star').mouseout(function(){
        revertStarsCssRating();
    });
}
    
    
/* Add a userbook on click */
function attachAddUserBook() {
    $(".addUserBookBtn").click(function(p_event) {
        p_event.preventDefault();        
        addUserBook(this);
    }); 
}

function changeStarsCssRating(rating){
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
function revertStarsCssRating() {
    changeStarsCssRating($('#hiddenRating').val());
}

function getUserBookDataString(sender) {
    id = getValue(sender, "book_Id");
    isbn10 = getValue(sender, "book_ISBN10");
    isbn13 = getValue(sender, "book_ISBN13");
    asin = getValue(sender, "book_ASIN");
    title = getValue(sender, "book_Title");
    description = getValue(sender, "book_Description");
    imageUrl = getValue(sender, "book_ImageUrl");
    smallImageUrl = getValue(sender, "book_SmallImageUrl");
    largeImageUrl = getValue(sender, "book_LargeImageUrl");
    author = getValue(sender, "book_Author");
    publisher = getValue(sender, "book_Publisher");
    publishingDate = getValue(sender, "book_PublishingDate");
    amazonUrl = getValue(sender, "book_AmazonUrl");
    nbOfPages = getValue(sender, "book_NbOfPages");
    language = getValue(sender, "book_Language");
    return "action=addUserBook&nonce=" + share1BookAjax.addUserBookNonce 
    + "&book_Id=" + id 
    + "&book_ISBN10=" + isbn10 
    + "&book_ISBN13=" + isbn13 
    + "&book_ASIN=" + asin 
    + "&book_Title=" + title
    + "&book_Description=" + description
    + "&book_ImageUrl=" + imageUrl 
    + "&book_SmallImageUrl=" + smallImageUrl 
    + "&book_LargeImageUrl=" + largeImageUrl 
    + "&book_Author=" + author 
    + "&book_Publisher=" + publisher
    + "&book_PublishingDate=" + publishingDate
    + "&book_AmazonUrl=" + amazonUrl 
    + "&book_NbOfPages=" + nbOfPages
    + "&book_Language=" + language;
};
    
function getValue(sender, classe){
    return escape($("." + classe, $(sender).parents(".book-data")).val());
}
                
function addUserBook(sender) {
    $("#loading #loadingMsg").html("Ajout en cours ..."); // affiche le masque de loading "chargement en cours..."
    $("#loading").show(); // affiche le masque de loading "chargement en cours..."
    $.ajax({  
        type: "POST",  
        url: share1BookAjax.url,  
        data: getUserBookDataString(sender),  
        success: function(data) {
            //$('#flashes-wrap').remove();
            $("#loading").hide(); 
            $('#page').append("<div id=\"flashes-wrap\"><div id=\"flashes-background\"></div><div id='flashes'><div id='flashes-close-button'></div><ul><li>" + data + "</li></ul></div></div>");
            $("#flashes-wrap").click(function() {
                $("#flashes-wrap").remove();
            });
        }  
    });    
}