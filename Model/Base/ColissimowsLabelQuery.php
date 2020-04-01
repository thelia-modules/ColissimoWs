<?php

namespace ColissimoWs\Model\Base;

use \Exception;
use \PDO;
use ColissimoWs\Model\ColissimowsLabel as ChildColissimowsLabel;
use ColissimoWs\Model\ColissimowsLabelQuery as ChildColissimowsLabelQuery;
use ColissimoWs\Model\Map\ColissimowsLabelTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Order;

/**
 * Base class that represents a query for the 'colissimows_label' table.
 *
 *
 *
 * @method     ChildColissimowsLabelQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildColissimowsLabelQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildColissimowsLabelQuery orderByOrderRef($order = Criteria::ASC) Order by the order_ref column
 * @method     ChildColissimowsLabelQuery orderByError($order = Criteria::ASC) Order by the error column
 * @method     ChildColissimowsLabelQuery orderByErrorMessage($order = Criteria::ASC) Order by the error_message column
 * @method     ChildColissimowsLabelQuery orderByTrackingNumber($order = Criteria::ASC) Order by the tracking_number column
 * @method     ChildColissimowsLabelQuery orderByLabelData($order = Criteria::ASC) Order by the label_data column
 * @method     ChildColissimowsLabelQuery orderByLabelType($order = Criteria::ASC) Order by the label_type column
 * @method     ChildColissimowsLabelQuery orderByWeight($order = Criteria::ASC) Order by the weight column
 * @method     ChildColissimowsLabelQuery orderBySigned($order = Criteria::ASC) Order by the signed column
 * @method     ChildColissimowsLabelQuery orderByWithCustomsInvoice($order = Criteria::ASC) Order by the with_customs_invoice column
 * @method     ChildColissimowsLabelQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildColissimowsLabelQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildColissimowsLabelQuery groupById() Group by the id column
 * @method     ChildColissimowsLabelQuery groupByOrderId() Group by the order_id column
 * @method     ChildColissimowsLabelQuery groupByOrderRef() Group by the order_ref column
 * @method     ChildColissimowsLabelQuery groupByError() Group by the error column
 * @method     ChildColissimowsLabelQuery groupByErrorMessage() Group by the error_message column
 * @method     ChildColissimowsLabelQuery groupByTrackingNumber() Group by the tracking_number column
 * @method     ChildColissimowsLabelQuery groupByLabelData() Group by the label_data column
 * @method     ChildColissimowsLabelQuery groupByLabelType() Group by the label_type column
 * @method     ChildColissimowsLabelQuery groupByWeight() Group by the weight column
 * @method     ChildColissimowsLabelQuery groupBySigned() Group by the signed column
 * @method     ChildColissimowsLabelQuery groupByWithCustomsInvoice() Group by the with_customs_invoice column
 * @method     ChildColissimowsLabelQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildColissimowsLabelQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildColissimowsLabelQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildColissimowsLabelQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildColissimowsLabelQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildColissimowsLabelQuery leftJoinOrder($relationAlias = null) Adds a LEFT JOIN clause to the query using the Order relation
 * @method     ChildColissimowsLabelQuery rightJoinOrder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Order relation
 * @method     ChildColissimowsLabelQuery innerJoinOrder($relationAlias = null) Adds a INNER JOIN clause to the query using the Order relation
 *
 * @method     ChildColissimowsLabel findOne(ConnectionInterface $con = null) Return the first ChildColissimowsLabel matching the query
 * @method     ChildColissimowsLabel findOneOrCreate(ConnectionInterface $con = null) Return the first ChildColissimowsLabel matching the query, or a new ChildColissimowsLabel object populated from the query conditions when no match is found
 *
 * @method     ChildColissimowsLabel findOneById(int $id) Return the first ChildColissimowsLabel filtered by the id column
 * @method     ChildColissimowsLabel findOneByOrderId(int $order_id) Return the first ChildColissimowsLabel filtered by the order_id column
 * @method     ChildColissimowsLabel findOneByOrderRef(string $order_ref) Return the first ChildColissimowsLabel filtered by the order_ref column
 * @method     ChildColissimowsLabel findOneByError(boolean $error) Return the first ChildColissimowsLabel filtered by the error column
 * @method     ChildColissimowsLabel findOneByErrorMessage(string $error_message) Return the first ChildColissimowsLabel filtered by the error_message column
 * @method     ChildColissimowsLabel findOneByTrackingNumber(string $tracking_number) Return the first ChildColissimowsLabel filtered by the tracking_number column
 * @method     ChildColissimowsLabel findOneByLabelData(string $label_data) Return the first ChildColissimowsLabel filtered by the label_data column
 * @method     ChildColissimowsLabel findOneByLabelType(string $label_type) Return the first ChildColissimowsLabel filtered by the label_type column
 * @method     ChildColissimowsLabel findOneByWeight(double $weight) Return the first ChildColissimowsLabel filtered by the weight column
 * @method     ChildColissimowsLabel findOneBySigned(boolean $signed) Return the first ChildColissimowsLabel filtered by the signed column
 * @method     ChildColissimowsLabel findOneByWithCustomsInvoice(boolean $with_customs_invoice) Return the first ChildColissimowsLabel filtered by the with_customs_invoice column
 * @method     ChildColissimowsLabel findOneByCreatedAt(string $created_at) Return the first ChildColissimowsLabel filtered by the created_at column
 * @method     ChildColissimowsLabel findOneByUpdatedAt(string $updated_at) Return the first ChildColissimowsLabel filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildColissimowsLabel objects filtered by the id column
 * @method     array findByOrderId(int $order_id) Return ChildColissimowsLabel objects filtered by the order_id column
 * @method     array findByOrderRef(string $order_ref) Return ChildColissimowsLabel objects filtered by the order_ref column
 * @method     array findByError(boolean $error) Return ChildColissimowsLabel objects filtered by the error column
 * @method     array findByErrorMessage(string $error_message) Return ChildColissimowsLabel objects filtered by the error_message column
 * @method     array findByTrackingNumber(string $tracking_number) Return ChildColissimowsLabel objects filtered by the tracking_number column
 * @method     array findByLabelData(string $label_data) Return ChildColissimowsLabel objects filtered by the label_data column
 * @method     array findByLabelType(string $label_type) Return ChildColissimowsLabel objects filtered by the label_type column
 * @method     array findByWeight(double $weight) Return ChildColissimowsLabel objects filtered by the weight column
 * @method     array findBySigned(boolean $signed) Return ChildColissimowsLabel objects filtered by the signed column
 * @method     array findByWithCustomsInvoice(boolean $with_customs_invoice) Return ChildColissimowsLabel objects filtered by the with_customs_invoice column
 * @method     array findByCreatedAt(string $created_at) Return ChildColissimowsLabel objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildColissimowsLabel objects filtered by the updated_at column
 *
 */
abstract class ColissimowsLabelQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \ColissimoWs\Model\Base\ColissimowsLabelQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\ColissimoWs\\Model\\ColissimowsLabel', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildColissimowsLabelQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildColissimowsLabelQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \ColissimoWs\Model\ColissimowsLabelQuery) {
            return $criteria;
        }
        $query = new \ColissimoWs\Model\ColissimowsLabelQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
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
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildColissimowsLabel|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ColissimowsLabelTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ColissimowsLabelTableMap::DATABASE_NAME);
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
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildColissimowsLabel A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ORDER_ID, ORDER_REF, ERROR, ERROR_MESSAGE, TRACKING_NUMBER, LABEL_DATA, LABEL_TYPE, WEIGHT, SIGNED, WITH_CUSTOMS_INVOICE, CREATED_AT, UPDATED_AT FROM colissimows_label WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildColissimowsLabel();
            $obj->hydrate($row);
            ColissimowsLabelTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildColissimowsLabel|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ColissimowsLabelTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ColissimowsLabelTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the order_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderId(1234); // WHERE order_id = 1234
     * $query->filterByOrderId(array(12, 34)); // WHERE order_id IN (12, 34)
     * $query->filterByOrderId(array('min' => 12)); // WHERE order_id > 12
     * </code>
     *
     * @see       filterByOrder()
     *
     * @param     mixed $orderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::ORDER_ID, $orderId, $comparison);
    }

    /**
     * Filter the query on the order_ref column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderRef('fooValue');   // WHERE order_ref = 'fooValue'
     * $query->filterByOrderRef('%fooValue%'); // WHERE order_ref LIKE '%fooValue%'
     * </code>
     *
     * @param     string $orderRef The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByOrderRef($orderRef = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($orderRef)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $orderRef)) {
                $orderRef = str_replace('*', '%', $orderRef);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::ORDER_REF, $orderRef, $comparison);
    }

    /**
     * Filter the query on the error column
     *
     * Example usage:
     * <code>
     * $query->filterByError(true); // WHERE error = true
     * $query->filterByError('yes'); // WHERE error = true
     * </code>
     *
     * @param     boolean|string $error The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByError($error = null, $comparison = null)
    {
        if (is_string($error)) {
            $error = in_array(strtolower($error), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::ERROR, $error, $comparison);
    }

    /**
     * Filter the query on the error_message column
     *
     * Example usage:
     * <code>
     * $query->filterByErrorMessage('fooValue');   // WHERE error_message = 'fooValue'
     * $query->filterByErrorMessage('%fooValue%'); // WHERE error_message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $errorMessage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByErrorMessage($errorMessage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($errorMessage)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $errorMessage)) {
                $errorMessage = str_replace('*', '%', $errorMessage);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::ERROR_MESSAGE, $errorMessage, $comparison);
    }

    /**
     * Filter the query on the tracking_number column
     *
     * Example usage:
     * <code>
     * $query->filterByTrackingNumber('fooValue');   // WHERE tracking_number = 'fooValue'
     * $query->filterByTrackingNumber('%fooValue%'); // WHERE tracking_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $trackingNumber The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByTrackingNumber($trackingNumber = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($trackingNumber)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $trackingNumber)) {
                $trackingNumber = str_replace('*', '%', $trackingNumber);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::TRACKING_NUMBER, $trackingNumber, $comparison);
    }

    /**
     * Filter the query on the label_data column
     *
     * Example usage:
     * <code>
     * $query->filterByLabelData('fooValue');   // WHERE label_data = 'fooValue'
     * $query->filterByLabelData('%fooValue%'); // WHERE label_data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $labelData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByLabelData($labelData = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($labelData)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $labelData)) {
                $labelData = str_replace('*', '%', $labelData);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::LABEL_DATA, $labelData, $comparison);
    }

    /**
     * Filter the query on the label_type column
     *
     * Example usage:
     * <code>
     * $query->filterByLabelType('fooValue');   // WHERE label_type = 'fooValue'
     * $query->filterByLabelType('%fooValue%'); // WHERE label_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $labelType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByLabelType($labelType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($labelType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $labelType)) {
                $labelType = str_replace('*', '%', $labelType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::LABEL_TYPE, $labelType, $comparison);
    }

    /**
     * Filter the query on the weight column
     *
     * Example usage:
     * <code>
     * $query->filterByWeight(1234); // WHERE weight = 1234
     * $query->filterByWeight(array(12, 34)); // WHERE weight IN (12, 34)
     * $query->filterByWeight(array('min' => 12)); // WHERE weight > 12
     * </code>
     *
     * @param     mixed $weight The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByWeight($weight = null, $comparison = null)
    {
        if (is_array($weight)) {
            $useMinMax = false;
            if (isset($weight['min'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::WEIGHT, $weight['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($weight['max'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::WEIGHT, $weight['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::WEIGHT, $weight, $comparison);
    }

    /**
     * Filter the query on the signed column
     *
     * Example usage:
     * <code>
     * $query->filterBySigned(true); // WHERE signed = true
     * $query->filterBySigned('yes'); // WHERE signed = true
     * </code>
     *
     * @param     boolean|string $signed The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterBySigned($signed = null, $comparison = null)
    {
        if (is_string($signed)) {
            $signed = in_array(strtolower($signed), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::SIGNED, $signed, $comparison);
    }

    /**
     * Filter the query on the with_customs_invoice column
     *
     * Example usage:
     * <code>
     * $query->filterByWithCustomsInvoice(true); // WHERE with_customs_invoice = true
     * $query->filterByWithCustomsInvoice('yes'); // WHERE with_customs_invoice = true
     * </code>
     *
     * @param     boolean|string $withCustomsInvoice The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByWithCustomsInvoice($withCustomsInvoice = null, $comparison = null)
    {
        if (is_string($withCustomsInvoice)) {
            $with_customs_invoice = in_array(strtolower($withCustomsInvoice), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::WITH_CUSTOMS_INVOICE, $withCustomsInvoice, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ColissimowsLabelTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ColissimowsLabelTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Thelia\Model\Order object
     *
     * @param \Thelia\Model\Order|ObjectCollection $order The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function filterByOrder($order, $comparison = null)
    {
        if ($order instanceof \Thelia\Model\Order) {
            return $this
                ->addUsingAlias(ColissimowsLabelTableMap::ORDER_ID, $order->getId(), $comparison);
        } elseif ($order instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ColissimowsLabelTableMap::ORDER_ID, $order->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrder() only accepts arguments of type \Thelia\Model\Order or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Order relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function joinOrder($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Order');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Order');
        }

        return $this;
    }

    /**
     * Use the Order relation Order object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\OrderQuery A secondary query class using the current class as primary query
     */
    public function useOrderQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Order', '\Thelia\Model\OrderQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildColissimowsLabel $colissimowsLabel Object to remove from the list of results
     *
     * @return ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function prune($colissimowsLabel = null)
    {
        if ($colissimowsLabel) {
            $this->addUsingAlias(ColissimowsLabelTableMap::ID, $colissimowsLabel->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the colissimows_label table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ColissimowsLabelTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ColissimowsLabelTableMap::clearInstancePool();
            ColissimowsLabelTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildColissimowsLabel or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildColissimowsLabel object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ColissimowsLabelTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ColissimowsLabelTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        ColissimowsLabelTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ColissimowsLabelTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ColissimowsLabelTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ColissimowsLabelTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ColissimowsLabelTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ColissimowsLabelTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ColissimowsLabelTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildColissimowsLabelQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ColissimowsLabelTableMap::CREATED_AT);
    }

} // ColissimowsLabelQuery
