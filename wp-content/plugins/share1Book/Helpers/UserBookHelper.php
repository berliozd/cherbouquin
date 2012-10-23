<?php

use Sb\Config\Model;

namespace Sb\Helpers;

/**
 * Description of UserBookHelper
 *
 * @author Didier
 */
class UserBookHelper {

    /**
     *
     * @return Config
     */
    private static function getConfig() {
        global $s1b;
        return $s1b->getConfig();
    }

    // $friendLibraryView : if true, then no link will be rendered
    public static function getStatusPictos(\Sb\Db\Model\UserBook $userBook, $friendLibraryView = false) {

        $config = self::getConfig();

        $lendings = $userBook->getLendings();
        $borrowings = $userBook->getBorrowings();

//        var_dump($lending);
        $isBorrowed = false;
        $isLent = false;
        $isOwned = $userBook->getIsOwned();
        $isWished = $userBook->getIsWished();
        $borrowedOnce = $userBook->getBorrowedOnce();

        $link = \Sb\Helpers\HTTPHelper::Link(\Sb\Entity\Urls::USER_LIBRARY_DETAIL, array("page" => \Sb\Entity\LibraryPages::LENDING_EDIT, "ubid" => $userBook->getId()));

        $borrowerName = "";
        $lenderName = "";
        $lendingStartDate = "";
        $borrowingStartDate = "";
        $oneActiveLending = false;
        $oneActiveBorrowing = false;
        $oneLendingWaitingInactivation = false;

        if ($lendings) {
            $lending = new \Sb\Db\Model\Lending;
            foreach ($lendings as $lending) {
                if ($lending->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE) {
                    $oneActiveLending = true;
                    $borrowerName = $lending->getBorrower_userbook()->getUser()->getFirstName() . " " . $lending->getBorrower_userbook()->getUser()->getLastName();
                    $lendingStartDate = $lending->getStartDate()->format(__("d/m/Y", "s1b"));
                }
                if ($lending->getState() == \Sb\Lending\Model\LendingState::WAITING_INACTIVATION) {
                    $oneLendingWaitingInactivation = true;
                }
            }
        }

        if ($borrowings) {
            $borrowing = new \Sb\Db\Model\Lending;
            foreach ($borrowings as $borrowing) {
                if ($borrowing->getState() != \Sb\Lending\Model\LendingState::IN_ACTIVE) {
                    $oneActiveBorrowing = true;
                    if ($borrowing->getUserbook())
                        $lenderName = $borrowing->getUserbook()->getUser()->getFirstName() . " " . $borrowing->getUserBook()->getUser()->getLastName();
                    elseif (($borrowing->getGuest()))
                        $lenderName = sprintf("%s (invité)", $borrowing->getGuest()->getName());
                    $borrowingStartDate = $borrowing->getStartDate()->format(__("d/m/Y", "s1b"));
                }
//                if ($borrowing->getState() == \Sb\Lending\Model\LendingState::WAITING_INACTIVATION) {
//                    $oneLendingWaitingInactivation = true;
//                }
            }
        }

        // Y'a t'il un prêt en cours pour ce livre ?
        if ($oneActiveLending) {
            $editLendingInfo = sprintf("Prêt&eacute; à %s depuis le %s", $borrowerName, $lendingStartDate);
            $isLent = true;
        } elseif ($oneActiveBorrowing) {
            $editLendingInfo = sprintf("Emprunté à %s depuis le %s", $lenderName, $borrowingStartDate);
            $isBorrowed = true;
        } else {
            $editLendingInfo = __("Prétez ce livre", "s1b");
        }


        $pictos = "";

        if ($isOwned)
            $pictos .= \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_OWNED);

        if ($isWished)
            $pictos .= \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_WISHED);

        if ($isLent) {
            if ($friendLibraryView) {
                $pictos .= \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_LENT);
            } else {
                $pictos .= sprintf("<a href=\"%s\" title=\"%s\">%s</a>", $link, $editLendingInfo, \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_LENT));
            }
        }

        if ($isBorrowed) {
            if ($friendLibraryView) {
                $pictos .= \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_BORROWED);
            } else {
                $pictos .= sprintf("<a href=\"%s\" title=\"%s\">%s</a>", $link, $editLendingInfo, \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_BORROWED));
            }
        }

        if (!$friendLibraryView) {
            if ($isOwned && !$isLent && !$isBorrowed && !$isWished) {
                if ($friendLibraryView) {
                    $pictos .= \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_LENDING);
                } else {
                    $pictos .= sprintf("<a href=\"%s\" title=\"%s\">%s</a>", $link, $editLendingInfo, \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_LENDING));
                }
            }
        }

        if ($oneLendingWaitingInactivation)
            $pictos .= sprintf("<a href=\"%s\" title=\"%s\">%s</a>", $link, $editLendingInfo, \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_RETURN_TO_VALIDATE));

//        if (!$isBorrowed && $borrowedOnce)
//            $pictos.=sprintf("<span title=\"%s\">%s</span>", __("Ce livre a fait l'objet d'un emprunt dans le passé.", "s1b"), \Sb\Helpers\BooksHelper::getStatusPicto(\Sb\Helpers\BooksHelper::PICTO_BORROWED_ONCE));
        return $pictos;
    }

}