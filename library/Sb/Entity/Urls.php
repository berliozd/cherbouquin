<?php

namespace Sb\Entity;

/**
 * Description of Urls
 *
 * @author Didier
 */
class Urls {

    const ACTIVATE = 'default/index/activate';                      // Zend : default / index / activate
    
    const CONTACT = "contact";                                      // Zend : default / static / index
    const ABOUT = "a-propos";                                       // Zend : default / static / index
    const TEAM = "lequipe";                                         // Zend : default / static / index
    const HOW_TO = "comment-ca-marche";                             // Zend : default / static / index
    const HELP_US = "nous-aider";                                   // Zend : default / static / index
    const PRESS = "presse";                                         // Zend : default / static / index
    const NEWSLETTERS = "bulletin";                                 // Zend : default / static / index
    const PRESS_REVIEW = "revues-de-presse";                        // Zend : default / static / index
    const PARTNERS = "nos-partenaires";                             // Zend : default / static / index
    const STEP_BY_STEP = "pas-a-pas";                               // Zend : default / static / index
    const LAST_CHRONICLES = "chroniques";
    const BLOGGERS_CHRONICLES = "chroniques/bloggeurs";             // Zend : default / chronicle / list
    const BOOKSTORES_CHRONICLES = "chroniques/libraires";           // Zend : default / chronicle / list
    const PRESS_REVIEWS_IN_MEDIAS = "revues-de-presse/articles";    // Zend : default / press-review / list
    const PRESS_REVIEWS_ALL_VIDEOS = "revues-de-presse/videos";     // Zend : default / press-review / list
    
    // account connection-registration-activation
    const LOGIN = 'default/index/log';                                              // Zend : default / index / log
    const LOGOUT = 'member/index/log-off';                                          // Zend : member / index / log-off
    const LOGIN_FACEBOOK = 'default/index/facebook-log';                            // Zend : default / index / facebook-log
    const SUBSCRIBE = 'default/index/register';                                     // Zend : default / index / register
    const REFUSE_INVITATION = 'default/index/refuse-invitation';                    // Zend : default / index / refuse-invitation
    const LOST_PASSWORD = 'default/index/lost-password';                            // Zend : default / index / lost-password
    // User
    const USER_LIBRARY = "default/library/index";                                   // Zend : default /library /index
    const USER_LIBRARY_DETAIL = "bibliotheque-detail"; // page
    const USER_HOME = "membre/a-la-page";                                           // Zend : member / index / index
    // User profile
    const MY_PROFILE = 'member/profile/index';                                      // Zend : member / profile / index
    const USER_PROFILE_EDIT = 'member/profile/edit';                                // Zend : member / profile / edit
    const USER_PROFILE_SUBMIT = 'member/profile/submit';                            // Zend : member / profile / submit
    const USER_PROFILE_GRAVATAR = 'member/profile/gravatar';                        // Zend : member / profile / gravatar
    const USER_PROFILE_SETTINGS = 'member/profile/settings';                        // Zend : member / profile / settings
    const USER_PROFILE_SUBMIT_SETTINGS = 'member/profile/submit-settings';          // Zend : member / profile / submit-settings
    const USER_PROFILE_DELETE_ACCOUNT = 'member/profile/delete';                    // Zend : member / profile / delete
    const USER_PROFILE_EDIT_PASSWORD = 'member/profile/edit-password';              // Zend : member / profile / edit-password
    const USER_PROFILE_SUBMIT_PASSWORD = 'member/profile/submit-password';          // Zend : member / profile / submit-password
    // User mailbox
    const USER_MAILBOX = 'member/mailbox/index';                                    // Zend : member / mailbox / index
    const USER_MAILBOX_DELETE_MESSAGE = 'member/mailbox/delete';                    // Zend : member / mailbox / delete
    const USER_MAILBOX_READ_MESSAGE = 'member/mailbox/read-message';                // Zend : member / mailbox / read
    const USER_MAILBOX_REPLY_MESSAGE = 'member/mailbox/reply';                      // Zend : member / mailbox / reply
    const USER_MAILBOX_SEND_MESSAGE = 'member/mailbox/send';                        // Zend : member / mailbox / send
    const USER_MAILBOX_RECOMMAND = "member/book/recommand";                         // Zend : member / book /recommand
    const USER_MAILBOX_SUBMIT_RECOMMAND = "member/book/submit-recommand";           // Zend : member / book /submit-recommand

    // User friends
    const USER_FRIENDS = 'member/friends/list';                                     // Zend : member / friends / list
    const USER_FRIENDS_WISHLIST = 'default/users/wish-list';                        // Zend : default / users / wish-list
    const USER_FRIENDS_SELECTION = 'member/friends/select';                         // Zend : member / friends / select
    const USER_FRIENDS_FRIENDS = 'member/friends/friends-of-friends';               // Zend : member / friends / friends-of-friends
    const USER_FRIENDS_INVITE = 'member/friends/show-invite-form';                  // Zend : member / friends / show-invite-form
    const USER_FRIENDS_INVITE_SUBMIT = 'member/friends/invite';                     // Zend : member / friends / invite
    const USER_FRIENDS_REQUEST = 'member/friends/request';                          // Zend : member / friends / request
    const USER_FRIENDS_PENDING_REQUEST = 'member/friends/pending-requests';         // Zend : member / friends / pending-requests
    const USER_FRIENDS_FIND = 'member/friends/search';                              // Zend : member / friends / search

    // Users
    const USER_PROFILE = 'default/users/profile';                                   // Zend : default / users / profile

    // Friend
    const FRIEND_LIBRARY = 'default/library/friend-library';

    // SEO Content pages
    const LAST_ADDED_BOOKS = 'livres/derniers-livres-ajoutes';                      // Zend : default / books / last-added
    const BLOW_OF_HEARTS_BOOKS = 'livres/coups-de-coeur';                           // Zend : default / books / blow-of-hearts
    const TOPS_BOOKS = 'livres/tops-des-livres';                                    // Zend : default / books / tops

    // Wished User Book
    const WISHED_USERBOOK_SET_AS_OFFERED = "default/wished-userbook/set-as-offered"; // Zend : default / wished-userbook/ set-as-offered
    
    // Userbook Gift
    const USERBOOK_GIFT_DISABLE = "userbook-gift/disable";                          // Zend : default / userbook-gift / disable
    
    // Userbook Gifts
    const USERBOOK_GIFTS_SEND_BY_EMAIL= "userbook-gifts/send-by-email";             // Zend : default / userbook-gifts / send-by-email
    
    const BOOK_WARN_OFFENSIVE_COMMENT = "book/warn-offensive-comment";              // Zend : default / book / warn-offensive-comment

    // Book search
    const BOOK_SEARCH_SUBMIT = "default/book-search/search";                        // Zend : default / book-search / search
    const BOOK_SEARCH_SHOW = "default/book-search/show";                            // Zend : default / book-search / show

    // UserBook
    const USER_BOOK_EDIT = "member/user-book/edit";                                 // Zend : member / user-book / edit
    const USER_BOOK_SUBMIT = "member/user-book/submit";                             // Zend : member / user-book / submit
}
