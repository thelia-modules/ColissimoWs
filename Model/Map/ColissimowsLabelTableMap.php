<?php

namespace ColissimoWs\Model\Map;

use ColissimoWs\Model\ColissimowsLabel;
use ColissimoWs\Model\ColissimowsLabelQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'colissimows_label' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ColissimowsLabelTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'ColissimoWs.Model.Map.ColissimowsLabelTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'colissimows_label';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\ColissimoWs\\Model\\ColissimowsLabel';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'ColissimoWs.Model.ColissimowsLabel';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 13;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 13;

    /**
     * the column name for the ID field
     */
    const ID = 'colissimows_label.ID';

    /**
     * the column name for the ORDER_ID field
     */
    const ORDER_ID = 'colissimows_label.ORDER_ID';

    /**
     * the column name for the ORDER_REF field
     */
    const ORDER_REF = 'colissimows_label.ORDER_REF';

    /**
     * the column name for the ERROR field
     */
    const ERROR = 'colissimows_label.ERROR';

    /**
     * the column name for the ERROR_MESSAGE field
     */
    const ERROR_MESSAGE = 'colissimows_label.ERROR_MESSAGE';

    /**
     * the column name for the TRACKING_NUMBER field
     */
    const TRACKING_NUMBER = 'colissimows_label.TRACKING_NUMBER';

    /**
     * the column name for the LABEL_DATA field
     */
    const LABEL_DATA = 'colissimows_label.LABEL_DATA';

    /**
     * the column name for the LABEL_TYPE field
     */
    const LABEL_TYPE = 'colissimows_label.LABEL_TYPE';

    /**
     * the column name for the WEIGHT field
     */
    const WEIGHT = 'colissimows_label.WEIGHT';

    /**
     * the column name for the SIGNED field
     */
    const SIGNED = 'colissimows_label.SIGNED';

    /**
     * the column name for the WITH_CUSTOMS_INVOICE field
     */
    const WITH_CUSTOMS_INVOICE = 'colissimows_label.WITH_CUSTOMS_INVOICE';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'colissimows_label.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'colissimows_label.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'OrderId', 'OrderRef', 'Error', 'ErrorMessage', 'TrackingNumber', 'LabelData', 'LabelType', 'Weight', 'Signed', 'WithCustomsInvoice', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'orderId', 'orderRef', 'error', 'errorMessage', 'trackingNumber', 'labelData', 'labelType', 'weight', 'signed', 'withCustomsInvoice', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(ColissimowsLabelTableMap::ID, ColissimowsLabelTableMap::ORDER_ID, ColissimowsLabelTableMap::ORDER_REF, ColissimowsLabelTableMap::ERROR, ColissimowsLabelTableMap::ERROR_MESSAGE, ColissimowsLabelTableMap::TRACKING_NUMBER, ColissimowsLabelTableMap::LABEL_DATA, ColissimowsLabelTableMap::LABEL_TYPE, ColissimowsLabelTableMap::WEIGHT, ColissimowsLabelTableMap::SIGNED, ColissimowsLabelTableMap::WITH_CUSTOMS_INVOICE, ColissimowsLabelTableMap::CREATED_AT, ColissimowsLabelTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'ORDER_ID', 'ORDER_REF', 'ERROR', 'ERROR_MESSAGE', 'TRACKING_NUMBER', 'LABEL_DATA', 'LABEL_TYPE', 'WEIGHT', 'SIGNED', 'WITH_CUSTOMS_INVOICE', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'order_id', 'order_ref', 'error', 'error_message', 'tracking_number', 'label_data', 'label_type', 'weight', 'signed', 'with_customs_invoice', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'OrderId' => 1, 'OrderRef' => 2, 'Error' => 3, 'ErrorMessage' => 4, 'TrackingNumber' => 5, 'LabelData' => 6, 'LabelType' => 7, 'Weight' => 8, 'Signed' => 9, 'WithCustomsInvoice' => 10, 'CreatedAt' => 11, 'UpdatedAt' => 12, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'orderId' => 1, 'orderRef' => 2, 'error' => 3, 'errorMessage' => 4, 'trackingNumber' => 5, 'labelData' => 6, 'labelType' => 7, 'weight' => 8, 'signed' => 9, 'withCustomsInvoice' => 10, 'createdAt' => 11, 'updatedAt' => 12, ),
        self::TYPE_COLNAME       => array(ColissimowsLabelTableMap::ID => 0, ColissimowsLabelTableMap::ORDER_ID => 1, ColissimowsLabelTableMap::ORDER_REF => 2, ColissimowsLabelTableMap::ERROR => 3, ColissimowsLabelTableMap::ERROR_MESSAGE => 4, ColissimowsLabelTableMap::TRACKING_NUMBER => 5, ColissimowsLabelTableMap::LABEL_DATA => 6, ColissimowsLabelTableMap::LABEL_TYPE => 7, ColissimowsLabelTableMap::WEIGHT => 8, ColissimowsLabelTableMap::SIGNED => 9, ColissimowsLabelTableMap::WITH_CUSTOMS_INVOICE => 10, ColissimowsLabelTableMap::CREATED_AT => 11, ColissimowsLabelTableMap::UPDATED_AT => 12, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'ORDER_ID' => 1, 'ORDER_REF' => 2, 'ERROR' => 3, 'ERROR_MESSAGE' => 4, 'TRACKING_NUMBER' => 5, 'LABEL_DATA' => 6, 'LABEL_TYPE' => 7, 'WEIGHT' => 8, 'SIGNED' => 9, 'WITH_CUSTOMS_INVOICE' => 10, 'CREATED_AT' => 11, 'UPDATED_AT' => 12, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'order_id' => 1, 'order_ref' => 2, 'error' => 3, 'error_message' => 4, 'tracking_number' => 5, 'label_data' => 6, 'label_type' => 7, 'weight' => 8, 'signed' => 9, 'with_customs_invoice' => 10, 'created_at' => 11, 'updated_at' => 12, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('colissimows_label');
        $this->setPhpName('ColissimowsLabel');
        $this->setClassName('\\ColissimoWs\\Model\\ColissimowsLabel');
        $this->setPackage('ColissimoWs.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('ORDER_ID', 'OrderId', 'INTEGER', 'order', 'ID', true, null, null);
        $this->addColumn('ORDER_REF', 'OrderRef', 'VARCHAR', true, 255, null);
        $this->addColumn('ERROR', 'Error', 'BOOLEAN', true, 1, false);
        $this->addColumn('ERROR_MESSAGE', 'ErrorMessage', 'VARCHAR', false, 255, '');
        $this->addColumn('TRACKING_NUMBER', 'TrackingNumber', 'VARCHAR', false, 64, '');
        $this->addColumn('LABEL_DATA', 'LabelData', 'CLOB', false, null, null);
        $this->addColumn('LABEL_TYPE', 'LabelType', 'VARCHAR', false, 4, null);
        $this->addColumn('WEIGHT', 'Weight', 'FLOAT', true, null, null);
        $this->addColumn('SIGNED', 'Signed', 'BOOLEAN', false, 1, false);
        $this->addColumn('WITH_CUSTOMS_INVOICE', 'WithCustomsInvoice', 'BOOLEAN', true, 1, false);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Order', '\\Thelia\\Model\\Order', RelationMap::MANY_TO_ONE, array('order_id' => 'id', ), 'CASCADE', 'RESTRICT');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? ColissimowsLabelTableMap::CLASS_DEFAULT : ColissimowsLabelTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (ColissimowsLabel object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ColissimowsLabelTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ColissimowsLabelTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ColissimowsLabelTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ColissimowsLabelTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ColissimowsLabelTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = ColissimowsLabelTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ColissimowsLabelTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ColissimowsLabelTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ColissimowsLabelTableMap::ID);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::ORDER_ID);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::ORDER_REF);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::ERROR);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::ERROR_MESSAGE);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::TRACKING_NUMBER);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::LABEL_DATA);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::LABEL_TYPE);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::WEIGHT);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::SIGNED);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::WITH_CUSTOMS_INVOICE);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::CREATED_AT);
            $criteria->addSelectColumn(ColissimowsLabelTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.ORDER_ID');
            $criteria->addSelectColumn($alias . '.ORDER_REF');
            $criteria->addSelectColumn($alias . '.ERROR');
            $criteria->addSelectColumn($alias . '.ERROR_MESSAGE');
            $criteria->addSelectColumn($alias . '.TRACKING_NUMBER');
            $criteria->addSelectColumn($alias . '.LABEL_DATA');
            $criteria->addSelectColumn($alias . '.LABEL_TYPE');
            $criteria->addSelectColumn($alias . '.WEIGHT');
            $criteria->addSelectColumn($alias . '.SIGNED');
            $criteria->addSelectColumn($alias . '.WITH_CUSTOMS_INVOICE');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(ColissimowsLabelTableMap::DATABASE_NAME)->getTable(ColissimowsLabelTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ColissimowsLabelTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ColissimowsLabelTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ColissimowsLabelTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a ColissimowsLabel or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ColissimowsLabel object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ColissimowsLabelTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \ColissimoWs\Model\ColissimowsLabel) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ColissimowsLabelTableMap::DATABASE_NAME);
            $criteria->add(ColissimowsLabelTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = ColissimowsLabelQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ColissimowsLabelTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ColissimowsLabelTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the colissimows_label table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ColissimowsLabelQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a ColissimowsLabel or Criteria object.
     *
     * @param mixed               $criteria Criteria or ColissimowsLabel object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ColissimowsLabelTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from ColissimowsLabel object
        }

        if ($criteria->containsKey(ColissimowsLabelTableMap::ID) && $criteria->keyContainsValue(ColissimowsLabelTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ColissimowsLabelTableMap::ID.')');
        }


        // Set the correct dbName
        $query = ColissimowsLabelQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // ColissimowsLabelTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ColissimowsLabelTableMap::buildTableMap();
