<?php

namespace Database;

use App\Exceptions\DatabaseQueryException;

class QueryBuilder {
    /**
     * PDO object used to execute the query.
     */
    protected \PDO $pdo;

    /**
     * Type of the query.
     */
    protected string $type = 'select';

    /**
     * Name of the table involved in the query.
     */
    protected string $table;

    /**
     * Maximum number of rows to return.
     */
    protected ?int $limit = null;

    /**
     * Number of rows to skip at the beginning of the result set.
     */
    protected ?int $skip = null;

    /**
     * Order of the result set.
     */
    protected ?string $order = 'ASC';

    /**
     * Column used for sorting the result set.
     */
    protected ?string $orderBy = null;

    /**
     * Columns to select in the query.
     *
     * @var array|string
     */
    protected $select = '*';

    /**
     * Conditions of the query.
     */
    protected array $conditions;

    /**
     * Parameters of the query.
     */
    protected $parameters;

    /**
     * SQL query.
     */
    protected string $query;

    /**
     * Data of the query.
     */
    protected ?array $data = null;

    /**
     * Constructor for the QueryBuilder class.
     * It initializes the PDO object and the query string.
     */
    public function __construct() {
        $this->pdo = Connection::getConnection();
        $this->query = '';
    }

    /**
     * Sets the table name for the query.
     *
     * @param string $table the name of the table
     *
     * @return $this the QueryBuilder object
     */
    public function table(string $table) {
        $this->table = $table;

        return $this;
    }

    /**
     * Specifies the columns to select in the query.
     *
     * @param array|string $select the columns to select
     *
     * @return $this the QueryBuilder object
     */
    public function select(array|string $select = '*') {
        $this->type = 'select';
        $this->select = $select;

        return $this;
    }

    /**
     * Adds a condition to the query.
     *
     * @param mixed $column the column to compare
     * @param mixed $operator (Optional) The operator to use in the comparison. Defaults to null.
     * @param mixed $value (Optional) The value to compare against. Defaults to null.
     * @param string $separator (Optional) The logical operator to use between this condition and the previous one. Defaults to 'AND'.
     *
     * @return $this the QueryBuilder object
     */
    public function where(mixed $column, mixed $operator = null, mixed $value = null, string $separator = 'AND') {
        if (is_callable($column)) {
            $this->conditions[] = $this->whereCallback($column, $separator);

            return $this;
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->conditions[] = [
            'separator' => $separator,
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Adds an OR condition to the query.
     *
     * @param mixed $column the column to compare
     * @param mixed $operator (Optional) The operator to use in the comparison. Defaults to null.
     * @param mixed $value (Optional) The value to compare against. Defaults to null.
     *
     * @return $this the QueryBuilder object
     */
    public function orWhere(mixed $column, mixed $operator = null, mixed $value = null) {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * Builds a nested condition callback.
     *
     * @param callable $callback the callback function to build the nested condition
     * @param null|string $separator (Optional) The logical operator to use between this condition and the previous one. Defaults to null.
     *
     * @return array the nested condition array
     */
    public function whereCallback(callable $callback, ?string $separator = 'AND') {
        $builder = new QueryBuilder();
        $callback($builder);
        $conditions = $builder->buildConditionsQuery(false);

        return [
            'separator' => $separator,
            'value' => $conditions,
            'child' => true,
        ];
    }

    /**
     * Returns the conditions of the query builder.
     *
     * @return array the conditions of the query builder
     */
    public function getConditions() {
        return $this->conditions;
    }

    /**
     * Executes the prepared statement.
     *
     * @return PDOStatement the executed statement
     */
    public function execute() {
        $statement = $this->prepare();
        $this->bindParams($statement);

        try {
            $statement->execute();
        } catch (\PDOException $e) {
            throw new DatabaseQueryException($e->getMessage());
        }

        if ('select' == $this->type) {
            $statement = $statement->fetchAll(\PDO::FETCH_OBJ);
        }

        return $statement;
    }

    /**
     * Retrieves the first record from the executed statement.
     *
     * @return mixed the first record as an object
     */
    public function first() {
        $this->limit(1);
        $statement = $this->execute();

        return $statement->fetchObject();
    }

    /**
     * Binds the parameters to the statement.
     *
     * @param PDOStatement $statement the statement to bind the parameters to
     */
    public function bindParams($statement) {
        foreach ($this->conditions ?? [] as $condition) {
            $statement->bindParam($condition['column'], $condition['value']);
        }
        foreach ($this->data ?? [] as $key => $value) {
            $statement->bindParam($key, $value);
        }
    }

    /**
     * Builds the conditions query.
     *
     * @param bool $where whether to include the WHERE keyword in the query
     *
     * @return string the built conditions query
     */
    public function buildConditionsQuery($where = true) {
        if (empty($this->conditions)) {
            return '';
        }

        $query = '';

        if ($where) {
            $query = ' WHERE ';
        }

        $first = true;
        foreach ($this->conditions as $condition) {
            if ($first) {
                $first = false;
            } else {
                $query .= sprintf(' %s ', $condition['separator']);
            }

            if (isset($condition['child']) && true === $condition['child']) {
                $query .= sprintf('(%s)', $condition['value']);
            } else {
                $paramKey = sprintf(':%s', $condition['column']);
                $query .= sprintf('`%s` %s %s', $condition['column'], $condition['operator'], $paramKey);
            }
        }

        return $query;
    }

    /**
     * Prepares the SQL query.
     *
     * @return PDOStatement the prepared statement
     */
    public function prepare() {
        return $this->pdo->prepare($this->toSql());
    }

    /**
     * Limits the number of rows returned by the query.
     *
     * @param int $limit the maximum number of rows to return
     *
     * @return $this the QueryBuilder object
     */
    public function limit(int $limit) {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Skips the specified number of rows at the beginning of the result set.
     *
     * @param int $skip the number of rows to skip
     *
     * @return $this the QueryBuilder object
     */
    public function skip(int $skip) {
        $this->skip = $skip;

        return $this;
    }

    /**
     * Sets the page of results to retrieve.
     *
     * @param int $page the page number to retrieve
     */
    public function page(int $page) {
        if ($this->limit) {
            $this->skip = ($page - 1) * $this->limit;
        }
    }

    /**
     * Sorts the result set by the specified column and order.
     *
     * @param string $column the column to sort by
     * @param string $order The order to sort in. Default is 'ASC'.
     *
     * @return $this the QueryBuilder object
     */
    public function orderBy(string $column, string $order = 'ASC') {
        $this->orderBy = $column;
        $this->order = $order;

        return $this;
    }

    /**
     * Updates the specified rows in the table.
     *
     * @param array $data an associative array of column names and their values
     *
     * @return $this the QueryBuilder object
     */
    public function update(array $data) {
        $this->type = 'update';
        $this->data = $data;

        return $this;
    }

    /**
     * Builds the set query for the update statement.
     *
     * @return string the set query
     */
    public function buildUpdateQuery() {
        if (empty($this->data)) {
            return '';
        }

        $query = ' SET ';

        $first = true;
        foreach ($this->data as $key => $value) {
            if ($first) {
                $first = false;
            } else {
                $query .= ', ';
            }

            $query .= sprintf('%s = :%s', $key, $key);
        }

        return $query;
    }

    /**
     * Builds the insert query for the insert statement.
     *
     * @return string the insert query
     */
    public function buildInsertQuery() {
        if (empty($this->data)) {
            return '';
        }

        $columns = implode(', ', array_keys($this->data));
        $placeholders = implode(', ', array_map(function($key) {
            return ':'.$key;
        }, array_keys($this->data)));

        return sprintf(' (%s) VALUES (%s)', $columns, $placeholders);
    }

    /**
     * Deletes the rows from the table.
     *
     * @return PDOStatement the executed statement
     */
    public function delete() {
        $this->type = 'delete';

        return $this->execute();
    }

    /**
     * Generates the start of the SQL query.
     *
     * @return string the SQL query start
     */
    public function getQueryStart() {
        return match ($this->type) {
            'select' => sprintf('SELECT %s FROM `%s`', implode(',', is_array($this->select) ? $this->select : [$this->select]), $this->table),
            'delete' => sprintf('DELETE FROM `%s`', $this->table),
            'update' => sprintf('UPDATE `%s`', $this->table),
            'insert' => sprintf('INSERT INTO `%s`', $this->table),
            default => throw new \Exception('Invalid query type'),
        };
    }

    /**
     * Generates the end of the SQL query.
     *
     * @return string the SQL query end
     */
    public function getQueryEnd() {
        $query = '';

        if ('select' == $this->type) {
            if ($this->limit && $this->skip) {
                $query .= sprintf(' LIMIT %s OFFSET %s', $this->limit, $this->skip);
            }

            if ($this->limit) {
                $query .= sprintf(' LIMIT %s', $this->limit);
            }

            if ($this->orderBy) {
                $query .= sprintf(' ORDER BY `%s` %s', $this->orderBy, $this->order);
            }
        }

        return $query;
    }

    /**
     * Generates the complete SQL query.
     *
     * @return string the SQL query
     */
    public function toSql() {
        return sprintf(
            '%s%s%s%s%s',
            $this->getQueryStart(),
            $this->buildUpdateQuery(),
            $this->buildInsertQuery(),
            $this->buildConditionsQuery(),
            $this->getQueryEnd()
        );
    }

    /**
     * Returns an associative array with all the bindings for the query.
     *
     * The returned array contains the values to be bound to the query,
     * which includes both the data to be inserted and the conditions of
     * the query.
     *
     * @return array the bindings for the query
     */
    public function getBindings() {
        // Initialize the result array with the data to be inserted
        $res = $this->data;

        // Iterate over the conditions and add them to the result array
        foreach ($this->conditions as $condition) {
            $res[$condition['column']] = $condition['value'];
        }

        // Return the resulting array
        return $res;
    }

    /**
     * Inserts the specified data into the table.
     *
     * @param array $data an associative array of column names and their values
     *
     * @return $this the QueryBuilder object
     */
    public function insert(array $data) {
        $this->type = 'insert';
        $this->data = $data;

        return $this;
    }

    /**
     * Checks if a table exists in the current database.
     *
     * @return bool true if the table exists, false otherwise
     */
    public function exists() {
        // The SQL query to check if a table exists
        $sql = 'SELECT 1 FROM information_schema.tables 
                WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1';

        // Prepare and execute the query
        $stmt = Connection::getConnection()->prepare($sql);
        $stmt->execute([$this->table]);

        // Return whether a row was fetched or not
        return $stmt->fetch() !== false;
    }

    /**
     * Executes the query and returns the result as an array of objects.
     *
     * @return array the result of the query
     */
    public function get() {
        // Execute the query and fetch all rows
        return $this->execute()->fetchAll(\PDO::FETCH_OBJ);
    }
}
