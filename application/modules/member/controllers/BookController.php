<?php
use Sb\Authentification\Service\AuthentificationSvc,
    Sb\Flash\Flash,
    Sb\Helpers\HTTPHelper,
    Sb\Db\Dao\UserBookDao,
    Sb\Db\Dao\UserDao,
    Sb\Db\Dao\BookDao,
    Sb\Db\Dao\MessageDao,
    Sb\Db\Model\Message,
    Sb\Helpers\MailHelper,
    Sb\Service\MailSvc,
    Sb\Entity\Urls;


/**
 *
 * @author Didier
 */
class Member_BookController extends Zend_Controller_Action {

    public function init() {

        // Check if user is connected
        AuthentificationSvc::getInstance()->checkUserIsConnected();
    }

    public function recommandAction() {

        try {
            $globalContext = new \Sb\Context\Model\Context();

            // getting user
            /* @var \Sb\Db\Model\User $user */
            $user = $globalContext->getConnectedUser();

            $potentialRecipients = $user->getFriendsForEmailing();
            if (count($potentialRecipients) <= 0) {
                Flash::addItem(__("Pas de destinataire possible. Vous devez ajouter des amis pour pouvoir envoyer des recommandations.", "s1b"));
                HTTPHelper::redirectToReferer();
            }

            // Getting book
            $bookId = $this->getParam("id");
            $book = $this->getBook($bookId);

            $this->setFriendsSelectionInModel();

            // Add to model
            $this->view->user = $user;
            $this->view->book = $book;
            $this->view->userBook = UserBookDao::getInstance()->getByBookIdAndUserId($globalContext->getConnectedUser()->getId(), $bookId);
            $this->view->bookLink = HTTPHelper::Link($book->getLink());
            $this->view->message = $this->getParam("message");

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    public function submitRecommandAction() {

        try {

            $globalContext = new \Sb\Context\Model\Context();

            $user = $globalContext->getConnectedUser();

            // Getting params
            $id = $this->getParam("id");
            $title = $this->getParam("title");
            $message = $this->getParam("message");
            $IdAddressee = $this->getParam("IdAddressee");

            // Getting book
            $book = $this->getBook($id);

            if (!empty($title) && !empty($message) && !empty($IdAddressee)) {
                $titleVal = trim($title);
                $messageVal = trim($message);
                $recipients = $IdAddressee;
                $recipientsIds = explode(",", $recipients);
                foreach ($recipientsIds as $recipientId) {
                    if (trim($recipientId) != "") {
                        $recipient = UserDao::getInstance()->get($recipientId);
                        if ($recipient) {
                            // adding message in db
                            $message = new Message;
                            $message->setSender($user);
                            $message->setRecipient($recipient);
                            $message->setIs_read(false);
                            $message->setTitle($titleVal);
                            $message->setMessage($messageVal);
                            MessageDao::getInstance()->add($message);

                            // Sending email if user authorized it
                            $userSetting = $recipient->getSetting();
                            if ($userSetting->getEmailMe() == 'Yes') {
                                $body = MailHelper::newMessageArrivedBody($user->getUserName());
                                MailSvc::getInstance()->send($recipient->getEmail(),
                                    sprintf(__("%s vous recommande %s ", "s1b"), $user->getUserName(), $book->getTitle()), $body);
                            }
                        }
                    }
                }
                Flash::addItem(__("Message envoyé.", "s1b"));
                HTTPHelper::redirect(Urls::USER_HOME);

            } else {
                Flash::addItem(__("Au moins l'un des champs n'est pas rempli", "s1b"));
                HTTPHelper::redirect(Urls::USER_MAILBOX_RECOMMAND, Array("message" => $message, "id" => $id));
            }

        } catch (\Exception $e) {
            Trace::addItem(sprintf("Une erreur s'est produite dans \"%s->%s\", TRACE : %s\"", get_class(), __FUNCTION__, $e->getTraceAsString()));
            $this->forward("error", "error", "default");
        }
    }

    private function setFriendsSelectionInModel() {
        if ($this->getParam("Friends")) {
            $friendSelectionsIds = $this->getParam("Friends");
            $friendList = array();
            $friendIdList = "";
            foreach ($friendSelectionsIds as $friendSelection) {
                $friend = UserDao::getInstance()->get($friendSelection);
                $friendList[] = $friend;
                $friendIdList .= $friend->getId() . ",";
            }

            // Add to model
            $this->view->friendList = $friendList;
            $this->view->friendIdList = $friendIdList;
        }
    }

    private function getBook($bookId) {
        if (!$bookId) {
            Flash::addItem(__("Vous devez sélectionner un livre.", "s1b"));
            HTTPHelper::redirectToReferer();
        }
        $book = BookDao::getInstance()->get($bookId);
        if (!$book) {
            Flash::addItem(__("Le livre n'existe pas.", "s1b"));
            HTTPHelper::redirectToReferer();
        }

        return $book;
    }

}
