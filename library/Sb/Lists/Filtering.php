<?php
namespace Sb\Lists;

/**
 * Description of Sb\Lists\Filtering
 *
 * @author Didier
 */
class Filtering {

    private $type;
    private $value;

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

}

?>
