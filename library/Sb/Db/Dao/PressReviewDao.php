<?php

namespace Sb\Db\Dao;

use Sb\Db\Model\Model;

/**
 *
 * @author Didier
 */
class PressReviewDao extends AbstractDao {

    const MODEL = "\\Sb\\Db\\Model\\PressReview";

    private static $instance;

    /**
     *
     * @return \Sb\Db\Dao\PressReviewDao
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new PressReviewDao();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(self::MODEL);
    }

    public function getList($criteria, $orderBy, $maxResults) {

        $dql = "SELECT model FROM " . self::MODEL . " model";
        
        // Get join parts of Dql query
        $joins = null;
        foreach ($criteria as $key => $value) {
            if ($value instanceof Model)
                $joins .= " JOIN model." . $key . " " . $key . " ";
        }
        $dql .= $joins;
        
        // Get the criteria part separated by AND
        foreach ($criteria as $key => $value) {
            if ($value instanceof Model)
                $criterias .= $key . ".id = " . $value->getId() . " AND ";
            else { // if the value is not a model, the it is an array with first element being the operator (=, LIKE) and the second element being the value to compare
                $operator = $value[0];
                $conditionValue = $value[1];
                if ($operator == "LIKE")
                    $conditionValue = "'%" . $conditionValue . "%'";
                $criterias .= "model." . $key . " " . $operator . " " . $conditionValue . " AND ";
            }
        }
        if ($criterias) {
            $criterias = substr($criterias, 0, strlen($criterias) - 5);
            $dql .= " WHERE " . $criterias;
        }
        
        // Get order by part of Dql query
        $orderBySql = null;
        foreach ($orderBy as $fieldName => $orientation) {
            $orderBySql .= $orderBySql ? ', ' : ' ORDER BY ';
            $dql .= $orderBySql . ' model.' . $fieldName . " " . $orientation;
        }
        
        $query = $this->entityManager->createQuery($dql);
        
        if ($maxResults)
            $query->setMaxResults($maxResults);
        else
            $query->setMaxResults(1);
        
        return $this->getResults($query);
    }

}
