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

}