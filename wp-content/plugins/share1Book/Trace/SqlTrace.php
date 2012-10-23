<?php

namespace Sb\Trace;

/**
 * Description of SqlTrace
 *
 * @author Didier
 */
class SqlTrace implements \Doctrine\DBAL\Logging\SQLLogger {

    public function startQuery($sql, array $params = null, array $types = null) {

        \Sb\Trace\Trace::addItem($sql . ($params ? "-- params : " . implode(",", $params) : "") . ($types ? " -- types : " . implode(",",
                                $types) : ""));
    }

    public function stopQuery() {

    }

}