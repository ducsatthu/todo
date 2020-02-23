<?php

namespace Dst\Todo\Core\Models;

abstract class Entity
{

    /**
     * @var PDO
     */
    protected $db;

    protected $tableName;

    /**
     * Connect DB simple
     * Entity constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        try {
            $this->db = new \PDO(PDO_DNS, USERNAME, PASSWORD);
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
    public function save()
    {
        try {
            $class = new \ReflectionClass($this);
            $tableName = strtolower($class->getShortName());

            $propsToImplode = [];

            foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyName = $property->getName();
                if ($propertyName !== 'id') { //Except id
                    $propsToImplode[] = '`' . $propertyName . '` = "' . $this->{$propertyName} . '"';
                }
            }

            $setClause = implode(',', $propsToImplode);

            if ($this->id > 0) {
                $sqlQuery = 'UPDATE `' . $tableName . '` SET ' . $setClause . ' WHERE id = ' . $this->id;
            } else {
                $sqlQuery = 'INSERT INTO `' . $tableName . '` SET ' . $setClause;
            }

//            var_dump($sqlQuery);die();
            $result = $this->db->exec($sqlQuery);

            if ($this->db->errorCode() !== '00000') {
                throw new \Exception($this->db->errorInfo()[2]);
            }

            return $result;
        } catch (\Exception $e) {
            //TODO: Save Logs
            return false;
        }
    }

    /**
     * Destroy record
     *
     * @return bool|int
     */
    public function destroy()
    {
        try {
            $class = new \ReflectionClass($this);
            $tableName = strtolower($class->getShortName());

            $sqlQuery = 'DELETE FROM `' . $tableName . '` WHERE  `id` = ' . $this->id;

            $result = $this->db->exec($sqlQuery);

            if ($this->db->errorCode() !== '00000') {
                throw new \Exception($this->db->errorInfo()[2]);
            }

            return $result;
        } catch (\Exception $e) {
            //TODO: Save Logs
            return false;
        }
    }


    /**
     * Morph function
     * @param array $object
     * @return object
     * @throws \ReflectionException
     */
    public function morph(array $object)
    {
        $class = new \ReflectionClass($this);

        $entity = $class->newInstance();

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (isset($object[$property->getName()])) {
                $property->setValue($entity, $object[$property->getName()]);
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
    public function find($options = [])
    {
        try {
            $class = new \ReflectionClass($this);
            $tableName = strtolower($class->getShortName());

            $result = [];
            $query = 'SELECT * FROM `' . $tableName . '`';
            $whereConditions = [];

            if (is_array($options) && !empty($options)) {
                foreach ($options as $key => $value) {
                    $whereConditions[] = '`' . $key . '` = "' . $value . '"';
                }
                $query .= " WHERE " . implode(' AND ', $whereConditions);
            } elseif (is_string($options)) {
                $query .= ' WHERE ' . $options;
            }

            $raw = $this->db->query($query);

            if ($this->db->errorCode() !== '00000') {
                throw new \Exception($this->db->errorInfo()[2]);
            }

            foreach ($raw as $rawRow) {
                $result[] = $this->morph($rawRow);
            }

            return $result;
        } catch (\Exception $e) {
            //TODO: Save Logs
            return null;
        }
    }
}