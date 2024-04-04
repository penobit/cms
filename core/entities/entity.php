<?php

namespace App\Entities;

use Database\QueryBuilder;

/**
 * Entity class is the base class for database entities.
 * This class provides common functionality for interacting with the database.
 */
class Entity {
    /**
     * The table name of the entity.
     *
     * @var string
     */
    protected static $table = '';

    /**
     * Returns a new QueryBuilder instance for the entity's table.
     *
     * @return QueryBuilder
     */
    public function query() {
        $db = new QueryBuilder();
        $db->table(static::$table);

        return $db;
    }

    /**
     * Adds a where clause to the query.
     *
     * @param string $column the column name
     * @param string $operator the comparison operator
     * @param mixed $value the value to compare
     * @param string $separator the separator between clauses
     *
     * @return QueryBuilder
     */
    public function where($column, $operator, $value, $separator) {
        return $this->query()->where($column, $operator, $value, $separator);
    }

    /**
     * Retrieves all records from the entity's table.
     *
     * @return array the result set
     */
    public function all() {
        return $this->query()->select('*')->execute();
    }

    /**
     * Retrieves a record by its id.
     *
     * @param int $id the id of the record to retrieve
     *
     * @return mixed the retrieved record, or null if not found
     */
    public function find($id) {
        return $this->query()->where('id', '=', $id)->first();
    }

    /**
     * Retrieves a record by its id, or throws an exception if not found.
     *
     * @param int $id the id of the record to retrieve
     *
     * @return mixed the retrieved record
     *
     * @throws \Exception if the record is not found
     */
    public function findOrFail($id) {
        $result = $this->find($id);

        if (is_null($result)) {
            throw new \Exception('Not found');
        }

        return $result;
    }

    /**
     * Inserts a new record into the entity's table.
     *
     * @param array $attributes the attributes of the new record
     *
     * @return int the id of the inserted record
     */
    public function create(array $attributes) {
        return $this->query()->insert($attributes);
    }

    /**
     * Updates a record by its id.
     *
     * @param int $id the id of the record to update
     * @param array $attributes the attributes to update
     *
     * @return int the number of affected rows
     */
    public function update($id, array $attributes) {
        return $this->query()->where('id', '=', $id)->update($attributes);
    }

    /**
     * Deletes a record by its id.
     *
     * @param int $id the id of the record to delete
     *
     * @return int the number of affected rows
     */
    public function delete($id) {
        return $this->query()->where('id', '=', $id)->delete();
    }
}
