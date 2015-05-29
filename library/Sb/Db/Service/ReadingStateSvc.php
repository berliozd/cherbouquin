<?php

namespace Sb\Db\Service;

/**
 * Description of ReadingState
 * @author Didier
 */
class ReadingStateSvc extends AbstractService {

    const READ_STATE_DATA_KEY = "readState";

    const READING_STATES_DATA_KEY = "allSates";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Service\ReadingStateSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\ReadingStateSvc();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(new \Sb\Db\Dao\ReadingStateDao(), "ReadingState");
    }

    public function getReadSate() {

        $dataKey = self::READ_STATE_DATA_KEY;
        $data = $this->getData($dataKey);
        if ($data === false) {
            $data = $this->getDao()
                ->getByCode("READ");
            $this->setData($dataKey, $data);
        }
        return $data;
    }

    public function getReadingStates() {

        $dataKey = self::READING_STATES_DATA_KEY;
        $data = $this->getData($dataKey);
        if ($data === false) {
            $data = $this->getDao()
                ->getAll();
            $this->setData($dataKey, $data);
        }
        return $data;
    }

}
