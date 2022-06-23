<?php
namespace AbstractSources;

abstract class AbstractModel
{
    // define these constants in your child class
    #const tableName = 'tableName';
    #const key       = ['course_id', 'season_id'];

    public final function getTableName()
    {
        return get_called_class()::tableName;
    }

    public final function getKey()
    {
        return get_called_class()::key;
    }

    /**
     * Insert new entry into self::tableName table.
     * @param  array $data - new data to insert
     * @return int         - quantity of inserted rows
     */
    public function insertNewEntry(array $data) : int
    {
        $res = 0;

        if (empty($data)) {
            return $res;
        }

        $dbh = \MVC\DBFactory::getDBH();

        $columns = '';
        $values  = '';

        foreach($data as $key => $value) {
            $columns .= $key.',';
            $values  .= ":".$key.",";
        }

        $columns = rtrim($columns, ',');
        $values  = rtrim($values, ',');

        $q = "INSERT INTO ".$this->getTableName()." ($columns) VALUES ($values)";

        $sth = $dbh->prepare($q);
        $res = $sth->execute($data);

        return $res;
    }

    /**
     * Update entry
     * @param array $key  - where-condition for UPDATE clause
     * @param array $data - data to update
     * @return int        - quantity updated rows
     */
    public function updateEntry(array $data) : int
    {
        $where = '';
        $keys  = $this->getKey();

        foreach ($keys as $key) {
            $where .= $key.' = :'.$key.' AND ';
        }

        $where = rtrim($where, ' AND ');

        $set = '';

        foreach ($data as $key => $val) {
            $set .= $key.' = :'.$key.',';
        }

        $set = rtrim($set, ',');

        $q = "UPDATE ".$this->getTableName()." SET ".$set." WHERE ".$where;

        $dbh = \MVC\DBFactory::getDBH();
        $sth = $dbh->prepare($q);
        $res = $sth->execute($data);

        return $res;
    }

    /**
     * get entry from the rable matched values in $whereData .
     * the values from $whereData are merged with 'AND'-condition.
     * @param string $tableName
     * @param array  $whereData
     * @return array
     */
    public function getEntriesWhere(array $data) : array
    {
        $out = array();

        if (empty($data)) {
            return $out;
        }

        $where = '';

        foreach ($data as $key => $val) {
            $where .= $key.' = :'.$key.' AND ';
        }
        $where = rtrim($where, ' AND ');

        $q = "SELECT * FROM ".$this->getTableName()." WHERE ".$where;

        $dbh = \MVC\DBFactory::getDBH();
        $sth = $dbh->prepare($q);
        $sth->execute($data);
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }

    /**
     * Delete entry with primary key.
     * @param  array $data - where-condition for entries to be deleted.
     * @return int         - quantity of inserted rows
     */
    public function  deleteEntryWith(array $data) : int
    {
        $res = 0;

        if (empty($data)) {
            return $res;
        }

        $dbh = \MVC\DBFactory::getDBH();

        $where = '';

        foreach ($data as $key => $value) {

            $where .= $key.' = :'.$key.' AND ';
        }

        $where = rtrim($where, ' AND ');

        $q = "DELETE FROM ".$this->getTableName()." WHERE $where";

        $sth = $dbh->prepare($q);
        $res = $sth->execute($data);

        return $res;
    }

}
