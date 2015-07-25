<?php

namespace Sb\Db\Dao;

/**
 * Description of \Sb\Db\Dao\AbstractDao
 * @author Didier
 */
abstract class AbstractDao {

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager = null;

    protected $entityName;

    private $cacheDuration = 3600; // Cache duration in seconds
    protected function __construct($entityName) {

        $this->entityName = $entityName;
        $this->initEntityManager();
    }

    private function initEntityManager() {
        // get EntityManager singleton
        $this->entityManager = \Sb\Db\EntityManager::getInstance();
    }

    /**
     *
     * @param type $id
     * @return \Sb\Db\Model\Model
     */
    public function get($id) {

        $result = $this->entityManager->find($this->entityName, $id);
        return $result;
    }

    public function getAll($criteria = null, $orderby = null, $limit = null) {

        if ($criteria || $orderby || $limit) {
            return $this->entityManager->getRepository($this->entityName)
                ->findBy($criteria, $orderby, $limit);
        } else {
            return $this->entityManager->getRepository($this->entityName)
                ->findAll();
        }
    }

    /**
     * Get list of items
     * @param array of criteria $criteria. each item is also an array :
     * - item 1 : tells is the criteria is model or not
     * - item 2 : operator to use in query ('=', 'LIKE', 'IN', 'NOT IN')
     * - item 3 : the criteria value to compare, can be a primitive value int or string, or a model, or an array of models
     * ex 1 : $criteria["keywords"] = array(false, "LIKE", $searchTerm );
     * ex 2 : $criteria["group.type"] = array(true, "=", $groupType);
     * ex 3 : $criteria["group.type"] = array(true, "NOT IN", $excludedGroupTypes);
     * @param array $orderBy array of order criteria, key being the fieldname and value the ordering direction ('DESC' or 'ASC')
     * @param int $maxResults
     * @return Ambigous <multitype:, \Doctrine\ORM\mixed, \Doctrine\ORM\Internal\Hydration\mixed, \Doctrine\DBAL\Driver\Statement, string>
     */
    public function getList($criteria, $orderBy, $maxResults) {

        $models = "model,";
        $joins = null;
        $criterias = null;

        // Parse all criterias
        foreach ($criteria as $key => $value) {

            // Get the criteria value, int, string, model or array of models
            $criteriaValue = $value[2];

            // Get if criteria passed is a model or an array of model
            $criteriaIsModel = $value[0];

            // Get the 'model part' X,X,X. It will be in query right after 'Select X,X,X'
            if ($criteriaIsModel && !strpos($key, "."))
                $models .= $key . ","; //

            // Get 'join part' of Dql query
            if ($criteriaIsModel) { // Value passed is a model or an array of models
                if (strpos($key, ".")) {
                    $keys = explode(".", $key);
                    $joins .= " JOIN model." . $keys[0] . " " . $keys[0] . " JOIN " . $key . " " . str_replace(".", "_", $key);
                } else {
                    if ($criteriaValue)
                        $joins .= " JOIN model." . $key . " " . $key . " ";
                    else // Make a left join in case no value are requested to compare the criteria. This is in order not to restrict the results. This is when criteria is just present to be added in
                         // the select part in order to be returned with the results, for example to have the book returned when requested a chronicle.
                        $joins .= " LEFT JOIN model." . $key . " " . $key . " ";
                }
            }

            // Get the 'criteria part' separated by AND
            $operator = $value[1];
            if (isset($criteriaValue)) {
                // Value passed is a model or an array of models
                if ($criteriaIsModel) {
                    // Get id or ids if array of model
                    if (is_array($criteriaValue)) {
                        $conditionValueIds = "";
                        foreach ($criteriaValue as $model)
                            $conditionValueIds .= $model->getId() . ",";
                        $conditionValueIds = substr($conditionValueIds, 0, strlen($conditionValueIds) - 1);
                        $criteriaValue = $conditionValueIds;
                    } else
                        $criteriaValue = $criteriaValue->getId(); //

                    // Transform for specific operator (like, in, not in)
                    if ($operator == "LIKE")
                        $criteriaValue = "'%" . $criteriaValue . "%'";
                    if ($operator == 'NOT IN' || $operator == 'IN')
                        $criteriaValue = "(" . $criteriaValue . ")";

                    if (strpos($key, "."))
                        $criterias .= str_replace(".", "_", $key) . ".id " . $operator . " " . $criteriaValue . " AND ";
                    else
                        $criterias .= $key . ".id " . $operator . " " . $criteriaValue . " AND ";
                } else { // if the value is not a model, it is an array with first element being the operator (=, LIKE) and the second element being the value to compare

                    if ($operator == "LIKE" && is_array($criteriaValue)) {
                        $criterias .= "(" . $this->getLikeQuery($criteriaValue, $key) . ") AND ";
                    } else {
                        if ($operator == "LIKE")
                            $criteriaValue = "'%" . $criteriaValue . "%'";
                        else if ($operator == 'NOT IN' || $operator == 'IN')
                            $criteriaValue = "(" . $criteriaValue . ")";
                        else
                            $criteriaValue = "'" . $criteriaValue . "'";

                        $criterias .= "model." . $key . " " . $operator . " " . $criteriaValue . " AND ";
                    }
                }
            }
        }

        // Remove the final ',' from the models string
        $models = substr($models, 0, strlen($models) - 1);

        // Build the main dql query
        $dql = "SELECT " . $models . " FROM " . $this->entityName . " model";

        // Add join part to dql query
        $dql .= $joins;

        // Add criterias to dql query
        if ($criterias) {
            $criterias = substr($criterias, 0, strlen($criterias) - 5);
            $dql .= " WHERE " . $criterias;
        }

        // Add order by part to Dql query
        if ($orderBy) {
            $orderBySql = null;
            foreach ($orderBy as $fieldName => $orientation) {
                $orderBySql .= $orderBySql ? ', ' : ' ORDER BY ';
                $orderBySql .= ' model.' . $fieldName . " " . $orientation;
            }
            $dql .= $orderBySql;
        }

        $query = $this->entityManager->createQuery($dql);

        if ($maxResults)
            $query->setMaxResults($maxResults);

        return $this->getResults($query);
    }

    public function getResults(\Doctrine\ORM\Query $query, $cacheId = null, $useCache = false) {

        $query->useResultCache($useCache, $this->cacheDuration, $cacheId);
        return $query->getResult();
    }

    public function getOneResult(\Doctrine\ORM\Query $query, $cacheId = null, $useCache = false) {

        $query->useResultCache($useCache, $this->cacheDuration, $cacheId);
        return $query->getOneOrNullResult();
    }

    /**
     * Get a cache id for cache item to store the result
     * @param type $func
     * @param type $args
     * @return string
     */
    public function getCacheId($func, $args) {

        $result = get_called_class() . "_" . $func . "_" . implode("-", array_values($args));
        return $result;
    }

    public function setCacheDuration($cacheDuration) {

        $this->cacheDuration = $cacheDuration;
    }

    public function bulkRemove($entities) {

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();
    }

    public function remove($entity) {

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param \Sb\Db\Model\Model $entity
     * @return boolean true if update occured successfuly
     */
    public function update(\Sb\Db\Model\Model $entity) {

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return true;
    }

    /**
     *
     * @param \Sb\Db\Model\Model $entity
     * @return boolean true if adding occured successfuly
     */
    public function add(\Sb\Db\Model\Model $entity) {

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return true;
    }

    /**
     * Get the "like" query part for a list of words
     * @param array of String $words
     * @return string the "like" query
     */
    private function getLikeQuery($words, $fieldName) {

        $likeWords = array();

        foreach ($words as $word)
            $likeWords[] = " model." . $fieldName . " LIKE '%" . $word . "%' ";

        $result = implode(" OR ", $likeWords);

        return $result;
    }

}