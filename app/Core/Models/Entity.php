<?php
namespace Dst\Todo\Core\Models;

abstract class Entity {

    /**
     * @var PDO
     */
    protected $db;

    public function __construct() {
        try {
            $this->db = new \PDO('mysql:host=localhost;dbname=blog','root', '');
        } catch (\Exception $e) {
            throw new \Exception('Error creating a database connection ');
        }
    }
    /**
     * Save method with refection
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function save() {
        $class = new \ReflectionClass($this);
        $tableName = strtolower($class->getShortName());

        $propsToImplode = [];

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $propsToImplode[] = '`'.$propertyName.'` = "'.$this->{$propertyName}.'"';
        }

        $setClause = implode(',',$propsToImplode);

        $sqlQuery = 'INSERT INTO `'.$tableName.'` SET '.$setClause.', id = '.$this->id;
        if ($this->id > 0) {
            $sqlQuery = 'UPDATE `'.$tableName.'` SET '.$setClause.' WHERE id = '.$this->id;
        }

        $result = self::$db->exec($sqlQuery);

        if (self::$db->errorCode()) {
            throw new \Exception(self::$db->errorInfo()[2]);
        }

        return $result;
    }
}