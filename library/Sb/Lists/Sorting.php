<?php

namespace Sb\Lists;

/**
 * Description of Sb\Lists\Sorting
 *
 * @author Didier
 */
class Sorting {

    private $field;
    private $direction;

    public function getField() {
        return $this->field;
    }

    public function setField($field) {
        $this->field = $field;
    }

    public function getDirection() {
        return $this->direction;
    }

    public function setDirection($direction) {
        $this->direction = $direction;
    }

}

?>
