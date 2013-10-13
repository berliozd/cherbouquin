<?php

namespace Sb\Entity;

/**
 * Description of Urls
 *
 * @author Didier
 */
class Urls {

    const BOOK_SEARCH = "resultats"; // library_fullwidth

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
    const LOGOUT = 'membre/deconnexion'; // user_logout
    const LOGIN_FACEBOOK = 'default/index/facebook-log';                            // Zend : default / index / facebook-log
    const SUBSCRIBE = 'membre/inscription'; //user_registration
    const REFUSE_INVITATION = 'membre/refus-invitation'; //user_refuse_invitation
    const LOST_PASSWORD = 'mot-de-passe-perdu';
    // User
    const USER_LIBRARY = "bibliotheque"; // library_fullwidth    
    const USER_LIBRARY_DETAIL = "bibliotheque-detail"; // page
    const USER_HOME = "membre/a-la-page";                                           // Zend : member / index / index
    // User profile
    const MY_PROFILE = 'member/profile/index';                                      // Zend : member / profile / index
    const USER_PROFILE_EDIT = 'member/profile/edit';                                // Zend : member / profile / edit
    const USER_PROFILE_SUBMIT = 'member/profile/submit';                            // Zend : member / profile / submit
    const USER_PROFILE_GRAVATAR = 'profil-membre/gravatar';         // user_profile_gravatar
    const USER_PROFILE_SETTINGS = 'profil-membre/parametrage'; // user_profile_settings
    const USER_PROFILE_DELETE_ACCOUNT = 'profil-membre/supprimer'; // user_profile_delete
    const USER_PROFILE_EDIT_PASSWORD = 'profil-membre/modification-mot-de-passe'; // user_profile_edit_password
    // User mailbox
    const USER_MAILBOX = 'messagerie'; // user_mailbox
    const USER_MAILBOX_DELETE_MESSAGE = 'messagerie/supprimer'; // user_message_delete
    const USER_MAILBOX_READ_MESSAGE = 'member/mailbox/read-message';                // Zend : member / mailbox / read
    const USER_MAILBOX_REPLY_MESSAGE = 'messagerie/repondre'; // user_message_read
    const USER_MAILBOX_SEND_MESSAGE = 'messagerie/envoyer'; // user_message_send
    const USER_MAILBOX_RECOMMAND = "messagerie/recommander"; // user_message_recommandation
    // User friends
    const USER_FRIENDS = 'member/friends/list';                                     // Zend : member / friends / list
    const USER_FRIENDS_WISHLIST = 'default/users/wish-list';                        // Zend : default / users / wish-list
    const USER_FRIENDS_SELECTION = 'member/friends/select';                         // Zend : default / friends / select
    const USER_FRIENDS_FRIENDS = 'amis/amis-d-amis'; // user_friends_of_friends
    const USER_FRIENDS_INVITE = 'amis/inviter'; // user_friends_invite
    //const USER_FRIENDS_REQUEST = 'amis/ami-requete'; // user_friends_request
    const USER_FRIENDS_REQUEST = 'member/friends/request';                          // Zend : member / friends / request
    const USER_FRIENDS_PENDING_REQUEST = 'amis/requete'; // user_friends_pending_request
    const USER_FRIENDS_FIND = 'amis/ajouter'; // user_friends_search

    // Users
    const USER_PROFILE = 'users/profile';
    
    // Friend    
    const FRIEND_LIBRARY = 'bibliotheque-damis';
    
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
}
