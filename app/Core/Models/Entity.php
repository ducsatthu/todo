<?php
namespace Dst\Todo\Core\Models;

abstract class Entity {

    /**
     * @var PDO
     */
    protected $db;

    protected $tableName;

    public function __construct() {
        try {
            $this->db = new \PDO('mysql:host=localhost;dbname=todo','root', '');
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
        try{
            $class = new \ReflectionClass($this);
            $tableName = strtolower($class->getShortName());

            $propsToImplode = [];

            foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                if($property !== 'id'){
                    $propsToImplode[] = '`'.$propertyName.'` = "'.$this->{$propertyName}.'"';
                }
            }

            $setClause = implode(',',$propsToImplode);

            if ($this->id > 0) {
                $sqlQuery = 'UPDATE `'.$tableName.'` SET '.$setClause.' WHERE id = '.$this->id;
            }else{
                $sqlQuery = 'INSERT INTO `'.$tableName.'` SET '.$setClause;
            }

            $result = $this->db->exec($sqlQuery);

            if ($this->db->errorCode() !== '00000') {
                throw new \Exception($this->db->errorInfo()[2]);
            }

            return $result;
        }catch (\Exception $e){
            echo "Some thing went wrong !";
            die();
        }
    }

    /**
     * Morph function
     * @param array $object
     * @return object
     * @throws \ReflectionException
     */
    public function morph(array $object) {
        $class = new \ReflectionClass($this);

        $entity = $class->newInstance();

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (isset($object[$property->getName()])) {
                $property->setValue($entity,$object[$property->getName()]);
            }
        }
        return $entity;
    }

    /**
     * Simple find where on table
     *
     * @param array $options
     * @return array
     *
     * TODO: refactor order + limit for paginate
     */
    public function find ($options = []) {
        try{
            $class = new \ReflectionClass($this);
            $tableName = strtolower($class->getShortName());

            $result = [];
            $query = 'SELECT * FROM `'.$tableName.'`';
            $whereConditions = [];

            if (is_array($options) && !empty($options)) {
                foreach ($options as $key => $value) {
                    $whereConditions[] = '`'.$key.'` = "'.$value.'"';
                }
                $query .= " WHERE ".implode(' AND ',$whereConditions);
            }elseif (is_string($options)) {
                $query .= ' WHERE '.$options;
            }

            $raw = $this->db->query($query);

            if ($this->db->errorCode() !== '00000') {
                throw new \Exception($this->db->errorInfo()[2]);
            }

            foreach ($raw as $rawRow) {
                $result[] = $this->morph($rawRow);
            }

            return $result;
        }catch (\Exception $e){
            echo "Some thing went wrong !";
            die();
        }
    }
}