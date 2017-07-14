<?php

namespace AppBundle\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use AppBundle\Model\Players;
use AppBundle\Model\PlayersPeer;
use AppBundle\Model\PlayersQuery;

/**
 * @method PlayersQuery orderById($order = Criteria::ASC) Order by the id column
 * @method PlayersQuery orderBySurname($order = Criteria::ASC) Order by the surname column
 * @method PlayersQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method PlayersQuery orderByTeam($order = Criteria::ASC) Order by the team column
 * @method PlayersQuery orderByAge($order = Criteria::ASC) Order by the age column
 *
 * @method PlayersQuery groupById() Group by the id column
 * @method PlayersQuery groupBySurname() Group by the surname column
 * @method PlayersQuery groupByName() Group by the name column
 * @method PlayersQuery groupByTeam() Group by the team column
 * @method PlayersQuery groupByAge() Group by the age column
 *
 * @method PlayersQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PlayersQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PlayersQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Players findOne(PropelPDO $con = null) Return the first Players matching the query
 * @method Players findOneOrCreate(PropelPDO $con = null) Return the first Players matching the query, or a new Players object populated from the query conditions when no match is found
 *
 * @method Players findOneBySurname(string $surname) Return the first Players filtered by the surname column
 * @method Players findOneByName(string $name) Return the first Players filtered by the name column
 * @method Players findOneByTeam(string $team) Return the first Players filtered by the team column
 * @method Players findOneByAge(int $age) Return the first Players filtered by the age column
 *
 * @method array findById(int $id) Return Players objects filtered by the id column
 * @method array findBySurname(string $surname) Return Players objects filtered by the surname column
 * @method array findByName(string $name) Return Players objects filtered by the name column
 * @method array findByTeam(string $team) Return Players objects filtered by the team column
 * @method array findByAge(int $age) Return Players objects filtered by the age column
 */
abstract class BasePlayersQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePlayersQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'AppBundle\\Model\\Players';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PlayersQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PlayersQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlayersQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlayersQuery) {
            return $criteria;
        }
        $query = new PlayersQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Players|Players[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PlayersPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PlayersPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Players A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Players A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `surname`, `name`, `team`, `age` FROM `players` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Players();
            $obj->hydrate($row);
            PlayersPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Players|Players[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Players[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PlayersPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PlayersPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PlayersPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PlayersPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the surname column
     *
     * Example usage:
     * <code>
     * $query->filterBySurname('fooValue');   // WHERE surname = 'fooValue'
     * $query->filterBySurname('%fooValue%'); // WHERE surname LIKE '%fooValue%'
     * </code>
     *
     * @param     string $surname The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterBySurname($surname = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($surname)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $surname)) {
                $surname = str_replace('*', '%', $surname);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::SURNAME, $surname, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the team column
     *
     * Example usage:
     * <code>
     * $query->filterByTeam('fooValue');   // WHERE team = 'fooValue'
     * $query->filterByTeam('%fooValue%'); // WHERE team LIKE '%fooValue%'
     * </code>
     *
     * @param     string $team The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByTeam($team = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($team)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $team)) {
                $team = str_replace('*', '%', $team);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayersPeer::TEAM, $team, $comparison);
    }

    /**
     * Filter the query on the age column
     *
     * Example usage:
     * <code>
     * $query->filterByAge(1234); // WHERE age = 1234
     * $query->filterByAge(array(12, 34)); // WHERE age IN (12, 34)
     * $query->filterByAge(array('min' => 12)); // WHERE age >= 12
     * $query->filterByAge(array('max' => 12)); // WHERE age <= 12
     * </code>
     *
     * @param     mixed $age The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function filterByAge($age = null, $comparison = null)
    {
        if (is_array($age)) {
            $useMinMax = false;
            if (isset($age['min'])) {
                $this->addUsingAlias(PlayersPeer::AGE, $age['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($age['max'])) {
                $this->addUsingAlias(PlayersPeer::AGE, $age['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayersPeer::AGE, $age, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Players $players Object to remove from the list of results
     *
     * @return PlayersQuery The current query, for fluid interface
     */
    public function prune($players = null)
    {
        if ($players) {
            $this->addUsingAlias(PlayersPeer::ID, $players->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
