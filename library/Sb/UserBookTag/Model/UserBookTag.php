<?php

namespace Sb\UserBookTag\Model;

/**
 * Description of UserBookTag
 *
 * @author Didier
 */
class UserBookTag {
    private $tag;
    private $selected;

    function __construct($tag, $selected) {
        $this->tag = $tag;
        $this->selected = $selected;
    }

    public function getTag() {
        return $this->tag;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }

    public function getSelected() {
        return $this->selected;
    }

    public function setSelected($selected) {
        $this->selected = $selected;
    }


}

?>
