<?php

\Sb\Trace\Trace::addItem(\Sb\Entity\LibraryPages::USERBOOK_IMPORT);

global $s1b;
$context = $s1b->getContext();
$config = $s1b->getConfig();

$tpl = new \Sb\Templates\Template("import");

if (!$s1b->getIsSubmit()) {
    $tpl->set("summary",
            __("Merci de sélectionner un fichier au format CSV, le séparateur de colonne devant être \",\" et le code ISBN des livres sur 10 caractères devant apparaître dans la première colonne.",
                    "share1book"));
} else {

    $isbnsInFile = getIsbnsInFile($context);
    \Sb\Trace\Trace::addItem(count($isbnsInFile) . " livres à rechercher.");

    if ($isbnsInFile) {

        if (count($isbnsInFile) > $config->getMaxImportNb()) {
            \Sb\Flash\Flash::addItem(sprintf(__("Il n'est pas possible d'importer plus de %s livres.", "s1b"),
                            $config->getMaxImportNb()));
        } else {

            //\Sb\Flash\Flash::addItem(count($isbnsInFile) . " ont été soumis dans votre fichier d'import.");
            \Sb\Flash\Flash::addItem(sprintf(__("%s livre(s) ont été soumis dans votre fichier d'import.", "s1b"),
                            count($isbnsInFile)));

            /// RECHERCHE DES LIVRES (base et amazon)
            $resultingBooks = searchBooks($isbnsInFile, $config);

            /// AJOUT DES LIVRES dans la base
            $booksAlReadyImported = array();
            $booksCorrectlyImported = array();
            loadBooks($resultingBooks, $booksAlReadyImported, $booksCorrectlyImported, $context);

            // MET A JOUR le résumé de l'import
            updateSummary($isbnsInFile, $tpl, $booksAlReadyImported, $booksCorrectlyImported, $context);
        }
    } else {
        $tpl->set("summary", "");
        \Sb\Flash\Flash::addItem(__("Aucun ISBN n'a pu être lu dans le fichier soumit.", "s1b"));
    }
}

echo $tpl->output();

//////////////////////////////////////////////////////////////////////////////////////////
function updateSummary($isbnsInFile, &$tpl, $booksAlReadyImported, $booksCorrectlyImported, \Sb\Context\Model\Context $context) {
    $alreadyImported = "";
    if (count($booksAlReadyImported) > 0)
        $alreadyImported = showTableSummary(sprintf(__("%s livre(s) déjà présents dans votre bibliothèque", "s1b"),
                        count($booksAlReadyImported)), $booksAlReadyImported, $context);

    $correctlyImported = "";
    if (count($booksCorrectlyImported) > 0)
        $correctlyImported = showTableSummary(sprintf(__("%s livre(s) correctement ajoutés à votre bibliothèque", "s1b"),
                        count($booksCorrectlyImported)), $booksCorrectlyImported, $context);
    $notImported = "";

    $isbnCorrectlyImported = array_map("getIsbnFromBook", $booksCorrectlyImported);
    $isbnAlreadyImported = array_map("getIsbnFromBook", $booksAlReadyImported);
    $isbnOK = array_merge($isbnAlreadyImported, $isbnCorrectlyImported);
    $isbnsNotImported = array_diff($isbnsInFile, $isbnOK);
    if (count($isbnsNotImported) > 0)
        $notImported = showNotImported($isbnsNotImported, $context);


    $tpl->set("summary", $correctlyImported . $alreadyImported . $notImported);
}

function loadBooks($resultingBooks, &$booksAlReadyImported, &$booksCorrectlyImported, \Sb\Context\Model\Context $context) {

    if ($resultingBooks) {

        $bookDao = \Sb\Db\Dao\BookDao::getInstance();

        foreach ($resultingBooks as $book) {

            if (!$book->getId()) { // il faut créer le Book
                \Sb\Trace\Trace::addItem("Création du livre : " . $book->getISBN10() . "(" . $book->getTitle() . ")");
                $bookId = $bookDao->Add($book);
                if ($bookId) { // le Book a été créé correctement
                    \Sb\Trace\Trace::addItem("Création OK");
                    if (addUserBook($bookId, $context)) {
                        $booksCorrectlyImported[] = $book;
                    }
                } else {
                    \Sb\Trace\Trace::addItem("NOT Création OK");
                }
            } else {
                // Le Book existe déjà, il y a des chances qui soit déjà affecté au user et il faut donc vérifier
                $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();
                $userBook = new \Sb\Db\Model\UserBook();
                $userBook = $userBookDao->getByBookIdAndUserId($context->getConnectedUser()->getId(), $book->getId());
                if ($userBook) { // le userBook existe déjà pour le user connecté
                    \Sb\Trace\Trace::addItem("Le livre " . $book->getISBN10() . " - " . $book->getTitle() . " existe déjà pour cet utilisateur.");
                    $booksAlReadyImported[] = $book;
                } else { // on peut créer le userBook
                    if (addUserBook($book->getId())) {
                        $booksCorrectlyImported[] = $book;
                    }
                }
            }
        }
    }
}

function searchBooks($isbnsInFile, Config $config) {

    $booksInBase = getBooksInBase($isbnsInFile);
    \Sb\Trace\Trace::addItem(count($booksInBase) . " livres trouvés dans la Base.");

    $resultingBooks = array();
    if ($booksInBase) {
        // récupération des books manquant dans amazon
        if (count($booksInBase) != count($isbnsInFile)) {
            $isbnsFromBase = array_map("getIsbnFromBook", $booksInBase);
            //var_dump($isbnsFromBase);
            $missedIsbns = array_diff($isbnsInFile, $isbnsFromBase);
            \Sb\Trace\Trace::addItem(count($missedIsbns) . " livres à rechercher dans Amazon.");

            $booksFromAmazon = getBooksFromAmazon($missedIsbns, $config);
            if ($booksFromAmazon) {
                \Sb\Trace\Trace::addItem(count($booksFromAmazon) . " livres trouvés dans Amazon.");
                $resultingBooks = array_merge($booksInBase, $booksFromAmazon);
                \Sb\Trace\Trace::addItem(count($resultingBooks) . " livres trouvés dans Amazon + Base.");
            } else {
                $resultingBooks = $booksInBase;
            }
        } else {
            $resultingBooks = $booksInBase;
        }
    } else { // aucun livre trouvé dans la base, rechercher dans Amazon
        $resultingBooks = getBooksFromAmazon($isbnsInFile, $config);
    }

    return $resultingBooks;
}

/**
 *
 * @global type $s1b
 * @return array of ISBN
 */
function getIsbnsInFile(\Sb\Context\Model\Context $context) {

    $uploaddir = $context->getBaseDirectory() . "var/uploads/";
    $uploadfile = $uploaddir . session_id() . "_" . basename($_FILES['importFile']['name']);
    if (move_uploaded_file($_FILES['importFile']['tmp_name'], $uploadfile)) {
        \Sb\Trace\Trace::addItem("File is valid, and was successfully uploaded.");
        $fileContent = file_get_contents($uploadfile);
        $lines = explode("\r\n", $fileContent);
        $isbns = array_filter(array_map("getIsbnFromLine", $lines), "isIsbn");
        // dédoublonage des isbns
        $isbns = array_unique($isbns);
        //var_dump($isbns);
        return $isbns;
    } else {
        \Sb\Trace\Trace::addItem("Possible file upload attack!");
    }
}

function getIsbnFromLine($line) {
    $cols = explode(",", $line);
    return $cols[0];
}

function getIsbnFromBook(\Sb\Db\Model\Book $book) {
    return $book->getISBN10();
}

function isIsbn($isbn) {
    if (strlen($isbn) == 10) {
        return true;
    }
    return false;
}

function getBooksInBase($isbns) {
    $bookDao = \Sb\Db\Dao\BookDao::getInstance();
    return $bookDao->GetListByISBN10($isbns);
}

function getBooksFromAmazon($isbns, Config $config) {

    \Sb\Trace\Trace::addItem(count($isbns) . " Isbns vont être requetés à Amazon : " . implode(",", $isbns));

    $booksFromAmazon = array();

    for ($index = 0; $index < count($isbns); $index = $index + 10) {

        $isbnsSlice = array_slice($isbns, $index, 10);
        \Sb\Trace\Trace::addItem(count($isbnsSlice) . " Isbns requetés par requête Amazon : " . implode(",", $isbnsSlice));

        $amazonService = new Zend_Service_Amazon($config->getAmazonApiKey(), 'FR', $config->getAmazonSecretKey());
        $responsesGroups = 'Small,ItemAttributes,Images,EditorialReview,Reviews';

        // Recherche d'une liste de livre
        $amazonResults = $amazonService->itemLookup(implode(",", $isbnsSlice),
                array('SearchIndex' => 'Books',
            'AssociateTag' => $config->getAmazonAssociateTag(),
            'ResponseGroup' => $responsesGroups,
            'IdType' => 'ISBN'));

        if ($amazonResults) {
            $i = 0;
            foreach ($amazonResults as $amazonResult) {
                $result = new \Sb\Db\Model\Book();
                \Sb\Db\Mapping\BookMapper::mapFromAmazonResult($result, $amazonResult);
                if ($result->IsValid()) {
                    $i++;
                    $booksFromAmazon[] = $result;
                }
                //var_dump($booksFromAmazon);
            }
            \Sb\Trace\Trace::addItem($i . " trouvés lors de cette requête amazon.");
        }
    }

    return $booksFromAmazon;
}

function addUserBook($bookId, \Sb\Context\Model\Context $context) {

    $userBook = new \Sb\Db\Model\UserBook();
    $userBook->setUserId($context->getConnectedUser()->getId());
    $userBook->setBookId($bookId);
    $userBook->setIsOwned(true);
    $userBookDao = \Sb\Db\Dao\UserBookDao::getInstance();
    $returnId = $userBookDao->Add($userBook);
    if ($returnId) {
        \Sb\Trace\Trace::addItem("Le livre a été ajouté à la biblio correctement.");
    } else {
        \Sb\Trace\Trace::addItem("KO : Le livre n'a pas été ajouté à la biblio.");
    }
    return $returnId;
}

function showTableSummary($title, $books, \Sb\Context\Model\Context $context) {

    $tplRows = array();
    $idx = 0;
    foreach ($books as $book) {
        $idx++;
        $tplRow = new \Sb\Templates\Template("import/tableRow");
        $tplRow->set("isbn", $book->getISBN10());
        $tplRow->set("title", $book->getTitle());
        $tplRow->set("index", $idx);
        $tplRows[] = $tplRow;
    }
    $rows = \Sb\Templates\Template::merge($tplRows);

    $tpl = new \Sb\Templates\Template("import/table");
    $tpl->set("rows", $rows);
    $tpl->set("tableTitle", $title);
    return $tpl->output();
}

function showNotImported($isbnsNotImported, \Sb\Context\Model\Context $context) {
    $tpl = new \Sb\Templates\Template("import/notImported");
    $tpl->set("isbns", implode(", ", $isbnsNotImported));
    $tpl->set("nbBooks", count($isbnsNotImported));
    return $tpl->output();
}