<?php


namespace Wolfren\Database;


use Exception;
use PDO;
use PDOException;
use Wolfren\File\File;
use Wolfren\Http\Request;
use Wolfren\Url\Url;

class Database
{
    /**
     * @var $instance
     */
    protected static $instance;

    /**
     * @var \PDO $connection
     */
    protected static $connection;
    /**
     * @var $table
     */
    protected static $table;
    /**
     * @var $select
     */
    protected static $select;
    /**
     * @var $join
     */
    protected static $join;
    /**
     * @var $where
     */
    protected static $where;
    /**
     * @var array $whereBinding
     */
    protected static $whereBinding = [];
    /**
     * @var $groupBy
     */
    protected static $groupBy;
    /**
     * @var $having
     */
    protected static $having;
    /**
     * @var array $havingBinding
     */
    protected static $havingBinding = [];
    /**
     * @var $orderBy
     */
    protected static $orderBy;
    /**
     * @var $direction
     */
    protected static $direction;
    /**
     * @var $limit
     */
    protected static $limit;
    /**
     * @var $offset
     */
    protected static $offset;
    /**
     * @var $query
     */
    protected static $query;
    /**
     * @var $bindings
     */
    protected static $bindings = [];
    /**
     * @var string $setter
     */
    protected static $setter;


    private function __construct()
    {

    }

    /**
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function select()
    {
        $select = func_get_args();
        $select = implode(',', $select);
        static::$select = $select;

        return static::instance();
    }

    /**
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    private static function instance()
    {
        static::connect();
        if (! self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    private function connect()
    {
        if (! static::$connection) {
            $database_data = File::require_file('config/database.php');
            extract($database_data);
            $dcn = 'mysql:dbname='.$database.';host='.$host.'';
            $option = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES $charset COLLATE $collation",
            ];
            try {
                static::$connection = new PDO($dcn, $username, $password, $option);

            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function orWhere($column, $operator, $value)
    {
        static::where($column, $operator, $value, 'OR');

        return static::instance();
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @param  null  $type
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function where($column, $operator, $value, $type = null)
    {
        $where = '`'.$column.'` '.$operator.' ?';
        if (! static::$where) {
            $statment = " WHERE ".$where;
        } else {
            if ($type == null) {
                $statment = " AND ".$where;
            } else {
                $statment = " $type ".$where;
            }
        }
        static::$where .= $statment;
        static::$whereBinding[] = htmlspecialchars($value);

        return static::instance();
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function rightJoin($table, $first, $operator, $second)
    {
        static::join($table, $first, $operator, $second, "RIGHT");

        return static::instance();
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     * @param $type
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function join($table, $first, $operator, $second, $type = "INNER")
    {
        static::$join .= " ".$type." JOIN ".$table." ON ".$first.$operator.$second;

        return static::instance();
    }

    /**
     * @param $table
     * @param $first
     * @param $operator
     * @param $second
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function leftJoin($table, $first, $operator, $second)
    {
        static::join($table, $first, $operator, $second, "LEFT");

        return static::instance();
    }

    /**
     * @param $column
     * @param  string  $direction
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function orderBy($column, $direction = "ASC")
    {
        $sep = static::$orderBy ? " , " : " ORDER BY ";
        $direction = strtoupper($direction);
        $direction = ($direction != null && in_array($direction, ["ASC", "DESC"])) ? $direction : "ASC";
        static::$direction = $direction;
        $statment = $sep.$column." ".$direction." ";
        static::$orderBy = $statment;

        return static::instance();
    }

    /**
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function groupBy()
    {
        $groupBy = func_get_args();
        $groupBy = " GROUP BY ".implode(',', $groupBy)." ";
        static::$groupBy = $groupBy;

        return static::instance();
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @param  null  $type
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function having($column, $operator, $value, $type = null)
    {
        $having = '`'.$column.'` '.$operator.' ?';
        if (! static::$having) {
            $statment = " HAVING ".$having;
        } else {
            if ($type == null) {
                $statment = " AND ".$having;
            } else {
                $statment = " $type ".$having;
            }
        }
        static::$having .= $statment;
        static::$havingBinding[] = htmlspecialchars($value);

        return static::instance();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function get()
    {
        $data = static::fetchExcute();
        $result = $data->fetchAll();

        return $result;
    }

    /**
     * @return mixed
     * @return \PDO $data
     * @throws \Exception
     */
    public static function fetchExcute()
    {
        static::query(static::$query);
        $query = trim(static::$query, ' ');
        $data = static::$connection->prepare($query);
        $data->execute(static::$bindings);

        static::clear();

        return $data;
    }

    public static function query($query = null)
    {
        static::instance();
        if ($query == null) {
            if (! static::$table) {
                throw new Exception("Unknown table.");
            }
            $query = "SELECT ";
            $query .= static::$select ?: '*';
            $query .= " FROM ".static::$table." ";
            $query .= static::$join." ";
            $query .= static::$where." ";
            $query .= static::$groupBy." ";
            $query .= static::$having." ";
            $query .= static::$orderBy." ";
            $query .= static::$limit." ";
            $query .= static::$offset." ";

            static::$query = $query;
            static::$bindings = array_merge(static::$whereBinding, static::$havingBinding);

            return static::instance();
        } else {

        }
    }

    /**
     *
     */
    public static function clear()
    {
        static::$select = '';
        static::$join = '';
        static::$where = '';
        static::$whereBinding = [];
        static::$groupBy = '';
        static::$having = '';
        static::$havingBinding = [];
        static::$orderBy = '';
        static::$limit = '';
        static::$instance = '';
        static::$offset = '';
        static::$bindings = [];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function all()
    {
        $data = static::fetchExcute();
        $result = $data->fetchAll();

        return $result;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getQuery()
    {
        static::query(static::$query);

        return static::$query;
    }

    /**
     * @param $data
     *
     * @return mixed
     * @throws \Exception
     */
    public static function insert($data)
    {
        $table = static::$table;
        $query = "INSERT INTO $table SET ";
        static::execute($data, $query);

        $object_id = static::$connection->lastInsertId();
        $object = static::table($table)->where('id', '=', $object_id)->first();

        return $object;
    }

    private static function execute($data = [], $query, $where = null)
    {
        static::instance();
        if (! static::$table) {
            throw  new  Exception("Unknown table ".static::$table.".");
        }
        foreach ($data as $key => $datum) {
            static::$setter .= " `$key` = ?, ";
            static::$bindings[] = filter_var($datum, FILTER_SANITIZE_STRING);
        }
        static::$setter = trim(static::$setter, ', ');
        $query .= static::$setter;
        $query .= $where != null ? static::$where." " : "";

        static::$bindings = $where != null ? array_merge(static::$bindings, static::$whereBinding) : static::$bindings;

        $data = static::$connection->prepare($query);
        $data->execute(static::$bindings);
        static::clear();

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function first()
    {
        $data = static::fetchExcute();
        $result = $data->fetch();

        return $result;
    }

    /**
     * @param $table
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function table($table)
    {
        static::$table = $table;

        return static::instance();
    }

    public static function paginate($per_page = 10)
    {
        static::query(static::$query);

        $query = trim(static::$query, ' ');
        $data = static::$connection->prepare($query);
        $data->execute();

        $pages = ceil($data->rowCount() / $per_page);
        $page = Request::get('page');

        $current_page = (! is_numeric($page) || Request::get('page') < 1) ? "1" : $page;
        $offset = ($current_page - 1) * $per_page;
        static::limit($per_page);
        static::offset($offset);
        static::query();
        $data = static::fetchExcute();
        $result = $data->fetchAll();

        return [
            'items' => $result, 'per_page' => $per_page, 'current_page' => $current_page, 'pages' => $pages,
            'offset' => $offset,
        ];
    }

    /**
     * @param $limit
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function limit($limit)
    {
        static::$limit = " LIMIT $limit ";

        return static::instance();
    }

    /**
     * @param $offset
     *
     * @return \Wolfren\Database\Database
     * @throws \Exception
     */
    public static function offset($offset)
    {
        static::$offset = " OFFSET $offset ";

        return static::instance();
    }

    public static function links($current_page, $pages)
    {
        $links = '';
        $from = $current_page - 2;
        $to = $current_page + 2;

        if ($from < 2) {
            $from = 2;
            $to = $from + 4;
        }
        if ($to >= $pages) {
            $diff = $to - $pages + 1;
            $from = ($from > 2) ? $from - $diff : 2;
            $to = $pages - 1;
        }

        if ($from < 2) {
            $from = 1;
        }
        if ($to >= $pages) {
            $to = $pages - 1;
        }
        if ($pages > 1) {
            $links .= "<ul class='pagination'>";
            $full_link = Url::url(Request::getFullUrl());
            $full_link = preg_replace('/\?page=(.*)/', '', $full_link);
            $full_link = preg_replace('/\&page=(.*)/', '', $full_link);
            $current_page_active = $current_page == 1 ? 'active' : '';
            $href = strpos($full_link, '?') ? ($full_link.'&page=1') : ($full_link.'?page=1');
            $links .= "<li class='page-item $current_page_active' ><a class='page-link' href='$href'>First</a></li>";

            for ($i = $from; $i <= $to; $i++) {
                $current_page_active = $current_page == $i ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link.'&page='.$i) : ($full_link.'?page='.$i);
                $links .= "<li class='page-item $current_page_active'><a class='page-link' href='$href'>$i</a></li>";
            }
            if ($pages > 1) {
                $current_page_active = $current_page == $pages ? 'active' : '';
                $href = strpos($full_link, '?') ? ($full_link.'&page='.$pages) : ($full_link.'?page='.$pages);
                $links .= "<li class='page-item $current_page_active' ><a class='page-link' href='$href'>Last</a></li>";
            }
        }

        return $links;
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws \Exception
     */
    public static function update($data)
    {
        $query = "UPDATE ".static::$table." SET";
        static::execute($data, $query, true);

        return true;
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws \Exception
     */
    public static function delete()
    {
        $query = "DELETE FROM ".static::$table." ";
        static::execute([], $query, true);

        return true;
    }

}