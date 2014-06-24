<?php
//
//use \Sb\Flash\Flash;
//use \Sb\Trace\Trace;
//use \Sb\Db\Model\UserEvent;
//use \Sb\Db\Dao\UserEventDao;
//use \Sb\Entity\EventTypes;
//
///**
// * Context :
// * From the book/view page, we clicked on "borrow" and arrived on a page where we could see all our friend with this book
// * After clicking on a friend name, here we are
// *
// * Input:
// * get-ubid : the userbook id we want to borrow
// *
// * Result:
// * userbook line creation
// * lending line creation
// *
// */
//Trace::addItem(\Sb\Entity\LibraryPages::LENDING_BORROW);
//
//global $s1b;
//$context = $s1b->getContext();
//
//$idUserBook = $_GET['ubid'];
//
//if ($idUserBook) {
//
//    $userBook = \Sb\Db\Dao\UserBookDao::getInstance()->get($idUserBook);
//
//    if ($userBook) {
//        $bookId = $userBook->getBook()->getId();
//
//        // We check that the userbook we want to bororow is really owned by a friend
//        $userBookCheck = \Sb\Db\Dao\UserBookDao::getInstance()->getBookInFriendsUserBook($bookId, $context->getConnectedUser()->getId());
//        if ($userBookCheck) {
//
//            // We check if the book is owned by the user we want to borrow the book from
//            if ($userBook->getIsOwned()) {
//                // We check that the book is not currently lent (no lending or an inactive lending)
//                if (!$userBook->getActiveLending()) {
//
//                    $existingUserBook = \Sb\Db\Dao\UserBookDao::getInstance()->getByBookIdAndUserId($context->getConnectedUser()->getId(), $bookId);
//
//                    // We check that the connect user doesn't already have the book
//                    if ($existingUserBook) {
//                        // the user already had that book but had deleted it
//                        if ($existingUserBook->getIs_deleted()) {
//                            $newUserBook = $existingUserBook;
//                            $newUserBook->setIs_deleted(false);
//                            $newUserBook->setLastModificationDate(new \DateTime());
//                            $newUserBook->setBorrowedOnce(true);
//                            $newUserBookPersisted = \Sb\Db\Dao\UserBookDao::getInstance()->update($newUserBook);
//                            Flash::addItem(__("Vous aviez déjà ce livre dans votre bibliothèque mais l'aviez supprimé.", "s1b"));
//                        } else {
//                            Flash::addItem(__("Vous avez déjà ce livre dans votre bibliothèque.", "s1b"));
//                            // Redirect to the main library page
//                            \Sb\Helpers\HTTPHelper::redirectToLibrary();
//                        }
//                    } else {
//                        // We create a userbook for the connected user
//                        $newUserBook = new \Sb\Db\Model\UserBook;
//                        $newUserBook->setBook($userBook->getBook());
//                        $newUserBook->setCreationDate(new \DateTime());
//                        $newUserBook->setLastModificationDate(new \DateTime());
//                        $newUserBook->setUser($context->getConnectedUser());
//                        $newUserBook->setBorrowedOnce(true);
//                        $newUserBookPersisted = \Sb\Db\Dao\UserBookDao::getInstance()->add($newUserBook);
//                    }
//
//                    if ($newUserBookPersisted) {
//
//                        // update lent userbook with Lent Once = 1
//                        $userBook->setLentOnce(true);
//                        \Sb\Db\Dao\UserBookDao::getInstance()->update($userBook);
//
//                        // Lending line creation
//                        $lending = new \Sb\Db\Model\Lending;
//                        $lending->setUserbook($userBook);
//                        $lending->setBorrower_userbook($newUserBook);
//                        $lending->setStartDate(new \DateTime());
//                        $lending->setState(\Sb\Lending\Model\LendingState::ACTIV);
//                        $lendingId = \Sb\Db\Dao\LendingDao::getInstance()->Add($lending);
//                        // if ok : prepare flash message
//                        if ($lendingId) {
//                            try {
//                                $userEvent = new UserEvent;
//                                $userEvent->setNew_value($lending->getId());
//                                $userEvent->setType_id(EventTypes::USER_BORROW_USERBOOK);
//                                $userEvent->setUser($context->getConnectedUser());
//                                UserEventDao::getInstance()->add($userEvent);
//                            } catch (Exception $exc) {
//                                Trace::addItem("erreur lors de l'ajout de l'évènement suite au prêt : " . $exc->getMessages());
//                            }
//                            Flash::addItem(sprintf(__("Le livre %s a été emprunté à %s et ajouté à votre bibliothèque.", "sharebook"), $userBook->getBook()->getTitle(), $userBook->getUser()->getFirstName() . " " . $userBook->getUser()->getLastName()));
//                        }
//                    }
//                } else {
//                    Flash::addItem(__("Ce livre fait l'objet d'un prêt en cours", "s1b"));
//                }
//            } else {
//                Flash::addItem(__("Ce livre n'est pas possédé par l'utilisateur à qui vous tentez d'emprunter ce livre.", "s1b"));
//            }
//        } else {
//            Flash::addItem(__("Vous n'êtes pas amis avec le propriétaire de ce livre.", "s1b"));
//        }
//    } else {
//        Flash::addItem(__("Le livre que vous voulez emprunter n'existe pas dans la base.", "s1b"));
//    }
//}
//
//// Redirect to the main library page
//\Sb\Helpers\HTTPHelper::redirectToLibrary();