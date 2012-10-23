<?php

namespace Sb\Entity;

/**
 * Description of Urls
 *
 * @author Didier
 */
class Urls {

    const BOOK_SEARCH = "resultats"; // library_fullwidth

    const ACTIVATE = 'membre/activation'; // user_activate
    
    const RECOMMAND_ON_FACEBOOK = "messagerie/recommander-facebook"; // user_recommandation_facebook

    const CONTACT = "contact"; // user_contact_form
    const ABOUT = "a-propos"; // user_about
    const TEAM = "lequipe"; // user_the_team
    const HOW_TO = "comment-ca-marche"; // user_the_team
    
    // account connection-registration-activation
    const LOGIN = 'membre/connexion'; // user_login
    const LOGOUT = 'membre/deconnexion'; // user_logout
    const LOGIN_FACEBOOK = 'membre/connexion-facebook'; // user_facebook_connect
    const SUBSCRIBE = 'membre/inscription'; //user_registration
    const REFUSE_INVITATION = 'membre/refus-invitation'; //user_refuse_invitation
    const LOST_PASSWORD = 'mot-de-passe-perdu';
    // User
    const USER_LIBRARY = "bibliotheque"; // library_fullwidth    
    const USER_LIBRARY_DETAIL = "bibliotheque-detail"; // page
    const USER_HOME = "membre/a-la-page"; // user_homepage
    // User profile
    const USER_PROFILE = 'profil-membre'; // user_profile
    const USER_PROFILE_EDIT = 'profil-membre/informations'; // user_profile_edit
    const USER_PROFILE_GRAVATAR = 'profil-membre/gravatar'; // user_profile_gravatar
    const USER_PROFILE_SETTINGS = 'profil-membre/parametrage'; // user_profile_settings
    const USER_PROFILE_DELETE_ACCOUNT = 'profil-membre/supprimer'; // user_profile_delete
    const USER_PROFILE_EDIT_PASSWORD = 'profil-membre/modification-mot-de-passe'; // user_profile_edit_password
    // User mailbox
    const USER_MAILBOX = 'messagerie'; // user_mailbox
    const USER_MAILBOX_DELETE_MESSAGE = 'messagerie/supprimer'; // user_message_delete
    const USER_MAILBOX_READ_MESSAGE = 'messagerie/lire'; // user_message_read
    const USER_MAILBOX_REPLY_MESSAGE = 'messagerie/repondre'; // user_message_read
    const USER_MAILBOX_SEND_MESSAGE = 'messagerie/envoyer'; // user_message_send
    const USER_MAILBOX_RECOMMAND = "messagerie/recommander"; // user_message_recommandation
    // User friends
    const USER_FRIENDS = 'amis'; // user_friends
    const USER_FRIENDS_WISHLIST = 'amis/liste-des-envies'; // user_friends_wishlist
    const USER_FRIENDS_SELECTION = 'amis/selection-destinataires'; // user_friends_form_selection
    const USER_FRIENDS_FRIENDS = 'amis/amis-d-amis'; // user_friends_of_friends
    const USER_FRIENDS_INVITE = 'amis/inviter'; // user_friends_invite
    const USER_FRIENDS_REQUEST = 'amis/ami-requete'; // user_friends_request
    const USER_FRIENDS_PENDING_REQUEST = 'amis/requete'; // user_friends_pending_request
    const USER_FRIENDS_FIND = 'amis/ajouter'; // user_friends_search

    // Friend
    const FRIEND_PROFILE = 'amis/profil';
    const FRIEND_LIBRARY = 'bibliotheque-damis';

}
