<?php
namespace Src\Utils;
// https://www.eddymens.com/blog/using-surreal-db-with-laravelphp

/**
 * Surreal fluent query
 *
 * @author  EDDYMENS
 * @license MIT (or other licence)
 */
class Surreal
{
    private $url;
    private $db;
    private $ns;
    private $user;
    private $pass;
    private $query;
    private $token;

    /**
     * Constructor
     *
     * @param  string $ns DB namespace
     * @param  string $db DB
     * @param  string $token JWT token
     * @param  string $user username
     * @param  string $pass password
     * @return object
     */
    public function __construct($ns, $db, $token, $user = NULL, $pass = NULL)
    {
        $this->url = $_ENV['DATABASE_URL'];
        $this->ns = $ns;
        $this->db = $db;

        if ($token !== NULL) {
            $this->token = 'Bearer ' . $token;
        } else {
            $this->user = $user;
            $this->pass = $pass;
            $this->token = 'Basic ' . base64_encode($this->user . ':' . $this->pass);
        }
    }

    /**
     * rawQuery
     * Run raw SQL
     * @param string $query query statement to run
     * @return array
     */
    public function rawQuery($query)
    {
        return $this->requestProcessor($query);
    }

    /**
     * exec
     * Fluent query terminitor
     * @return array
     */
    public function exec()
    {
        return $this->rawQuery($this->query);
    }

    /**
     * tables
     * Specify table to run statement against
     * @param string $tables list of tables
     * @return $this
     */
    public function tables($tables)
    {
        $tables = func_get_args();
        $stringTables = implode(', ', $tables);
        $this->query .= ' ' . $stringTables;
        return $this;
    }

    /**
     * select
     * Select records
     * @param string $fields list of fields to fetch
     * @return $this
     */
    public function select($fields = ' * ')
    {
        $providedFields = func_get_args();
        $stringFields = implode(', ', $providedFields);
        $this->query .= ' SELECT ' . $stringFields . ' FROM';
        return $this;
    }

    /**
     * where
     * Where statement
     * @param string $statement fields and condition combo eg: age > 18
     * @return $this
     */
    public function where($statement)
    {
        $this->query .= ' WHERE ' . $statement;
        return $this;
    }

    /**
     * andWhere
     * And Where statement
     * @param string $statement fields and condition combo eg: age > 18
     * @return $this
     */
    public function andWhere($statement)
    {
        $this->query .= ' AND ' . $statement;
        return $this;
    }

    /**
     * orWhere
     * Or Where statement
     * @param string $statement fields and condition combo eg: age > 18
     * @return $this
     */
    public function orWhere($statement)
    {
        $this->query .= ' OR ' . $statement;
        return $this;
    }

    /**
     * groupBy
     * Group records based on column
     * @param string $field field to group by
     * @return $this
     */
    public function groupBy($field)
    {
        $this->query .= ' GROUP BY ' . $field;
        return $this;
    }

    /**
     * split
     * Split records based on column
     * @param string $field field to split by
     * @return $this
     */
    public function split($field)
    {
        $this->query .= ' SPLIT ' . $field;
        return $this;
    }

    /**
     * orderBy
     * Order records based on column
     * @param string $field field to order by
     * @return $this
     */
    public function orderBy($field)
    {
        $this->query .= ' ORDER BY ' . $field;
        return $this;
    }

    /**
     * start
     * Specify index to start fetching records from
     * @param integer $count index to start fetching from
     * @return $this
     */
    public function start($count)
    {
        $this->query .= ' START ' . $count;
        return $this;
    }

    /**
     * limit
     * Number of records to return
     * @param integer $count record size
     * @return $this
     */
    public function limit($count)
    {
        $this->query .= ' LIMIT ' . $count;
        return $this;
    }

    /**
     * timeout
     * When to timeout request
     * @param integer $time in seconds
     * @return $this
     */
    public function timeout($time)
    {
        $this->query .= ' TIMEOUT ' . $time . 's';
        return $this;
    }

    /**
     * parallel
     * Fetch multiple records in parallel
     * @return $this
     */
    public function parallel()
    {
        $this->query .= ' PARALLEL ';
        return $this;
    }

    /**
     * contains
     * Fetch records if column contains provided word
     * @param string $word word to search for
     * @return $this
     */
    public function contains($word)
    {
        $this->query .= ' CONTAINS ' . $word;
        return $this;
    }

    /**
     * delete
     * delete a record
     * @param string $table table to delete record from
     * @return $this
     */
    public function delete($tables)
    {
        $this->query .= ' DELETE ';
        $this->tables($tables);
        return $this;
    }

    /**
     * return
     * Used to specify response type
     * @param string $type NONE | BEFORE | AFTER | DIFF
     * @return $this
     */
    public function return($type)
    {
        $this->query .= ' RETURN ' . strtoupper($type);
        return $this;
    }

    /**
     * create
     * Add new record
     * @param string $table Table to insert record
     * @return $this
     */
    public function create($table)
    {
        $this->query .= ' CREATE ';
        $this->tables($table);
        return $this;
    }

    /**
     * update
     * Update existing record
     * @param string $table table to update record
     * @return $this
     */
    public function update($table)
    {
        $this->query .= ' UPDATE ';
        $this->tables($table);
        return $this;
    }

    /**
     * data
     * Serialize data for create and update actions
     * @param string $data data to serialize
     * @return $this
     */
    public function data($data)
    {
        $data = json_encode($data);
        $this->query .= " CONTENT $data;";
        return $this;
    }

    public function patch($data)
    {
        $data = json_encode($data);
        $this->query .= " PATCH $data;";
        return $this;
    }

    /**
     * relate
     * Create relationship between records in different tables
     * @param string $table first table in relationship
     * @return $this
     */
    public function relate($table)
    {
        $this->query .= ' RELATE ';
        $this->tables($table);
        return $this;
    }

    /**
     * write
     * Specify second table to use in relationship
     * @param string $table second table to use in relationship
     * @return $this
     */
    public function write($table)
    {
        $this->query .= ' ->write-> ';
        $this->tables($table);
        return $this;
    }

    /**
     * merge
     * Append data to existing record
     * @param string $data new record to append
     * @return $this
     */
    public function merge($data)
    {
        $data = json_encode($data);
        $this->query .= " MERGE $data;";
        return $this;
    }

    /**
     * toSQL
     * Return generated SQL statement
     * @return string
     */
    public function toSQL()
    {
        return $this->query;
    }

    /**
     * subQuery
     * Add subquery to query
     * @param func $funcQuery anonymous function containing subquery
     * @return $this
     */
    public function subQuery($funcQuery)
    {
        $this->query .= ' (';
        $funcQuery();
        $this->query .= ' )';
        return $this;
    }

    /**
     * begin
     * Mark the start of a transaction
     * @return $this
     */
    public function begin()
    {
        $this->query .= ' BEGIN TRANSACTION; ';
        return $this;
    }

    /**
     * commit
     * Mark the end of a transaction
     * @return $this
     */
    public function commit()
    {
        $this->query .= ' COMMIT TRANSACTION; ';
        return $this;
    }

    /**
     * cancel
     * Cancel a transaction
     * @return $this
     */
    public function cancel()
    {
        $this->query .= ' CANCEL TRANSACTION; ';
        return $this;
    }

    private function requestProcessor($query)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: ' . $this->token,
                'Content-Type: application/json',
                'NS: ' . $this->ns,
                'DB: ' . $this->db
            ]
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return JSON_decode($response);
    }
}
