<?php

namespace Sb\Db\Model;

/**
 *
 * @author Didier
 */
interface Model {

    public function IsValid();

    public function getId();
    public function setId($id);

//    public function getCreationDate();
//    public function setCreationDate($creationDate);
//
//    public function getLastModificationDate();
//    public function setLastModificationDate($lastModificationDate);
}

?>
