<?php

namespace Sb\View;

class UserEvents extends \Sb\View\AbstractView {

    private $userEvents;

    function __construct($userEvents) {
        $this->userEvents = $userEvents;
        parent::__construct();
    }

    public function get() {

        if ($this->userEvents) {

            $templatesEvents = "";
            $count = count($this->userEvents);
            $i = 0;
            foreach ($this->userEvents as $userEvent) {
                $i++;
                $userEventView = new \Sb\View\UserEvent($userEvent, ($i != $count));
                $templatesEvents .= $userEventView->get();
            }

            $tpl = new \Sb\Templates\Template("userEvents");
            $tpl->set("events", $templatesEvents);

            return $tpl->output();
        }else
            return "";
    }

}
