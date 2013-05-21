$(function() {

    // next and prev buttons
    var nextButton = $('.nextButton');
    var prevButton = $('.prevButton');
    // wrapper
    var list = $('.nr-list');
    var pages = list.find('.nr-page');
    // how many nr-pages
    var pagesNumber = pages.length;
    // the default nr-page is the first one
    var pageIndex = 1;

    /*
     * shows next nr-page if exists: the next nr-page fades in also checks if the
     * button should get disabled
     */
    nextButton.bind('click', function(e) {
        var $this = $(this);
        prevButton.removeClass('disabled');
        ++pageIndex;
        if (pageIndex == pagesNumber)
            $this.addClass('disabled');
        if (pageIndex > pagesNumber) {
            pageIndex = pagesNumber;
            return;
        }
        pages.hide();
        list.find('.nr-page:nth-child(' + pageIndex + ')').fadeIn();
        e.preventDefault();
    });
    /*
     * shows previous nr-page if exists: the previous nr-page fades in also checks if
     * the button should get disabled
     */
    prevButton.bind('click', function(e) {
        var $this = $(this);
        nextButton.removeClass('disabled');
        --pageIndex;
        if (pageIndex == 1)
            $this.addClass('disabled');
        if (pageIndex < 1) {
            pageIndex = 1;
            return;
        }
        pages.hide();
        list.find('.nr-page:nth-child(' + pageIndex + ')').fadeIn();
        e.preventDefault();
    });

});