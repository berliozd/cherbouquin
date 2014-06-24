<?php
//
//\Sb\Trace\Trace::addItem(\Sb\Entity\LibraryPages::LENDING_VALIDATE);
//
//global $s1b;
//
//if (array_key_exists("lid", $_GET)) {
//    $lendingId = $_GET["lid"];
//
//    $lendingDao = \Sb\Db\Dao\LendingDao::getInstance();
//    $lending = $lendingDao->GetById($lendingId);
//
//    if ($lending) {
//        $lending->setState(\Sb\Lending\Model\LendingState::ACTIV);
//        $lending->setStartDate(new \DateTime());
//        $lending->setLastModificationDate(new \DateTime());
////        if ($lendingDao->Update($lending, $lendingId)) {
//            \Sb\Flash\Flash::addItem(__("Le prêt à été validé.", "s1b"));
//        }
//    } else {
//        \Sb\Flash\Flash::addItem(__("L'identifiant reçu ne correspond à aucun prêt.", "s1b"));
//    }
//} else {
//    \Sb\Flash\Flash::addItem(__("Identifiant manquant", "s1b"));
//}
//\Sb\Helpers\HTTPHelper::redirectToLibrary();