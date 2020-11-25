<?php
namespace RyanKikta {

    use PDOStatement;

class PDO_DB
{
    /**
     * @var PDO
     */
    protected $dblink;

    /**
     * @var boolean
     */
    protected $dirty;

    /**
     * @var PDOStatement
     */
    public $statement;

    /**
     * @var \RyanKikta\PDO_Parameter[]
     */
    public $params;

    /**
     * @var string
     */
    protected $_query;

    function __construct($dblink) {
        $this->dblink = $dblink;
    }

    function Query($sql = '') {
        if (!empty($sql)) {
            $this->_query = $sql;
            $this->dirty = TRUE;
        }
        return $this->_query;
    }

    /**
     * Simple wrapper around pdo_call to return the value at 0,0 by default.
     * column will implicitly address the associative column provided, but 0,0 by default.
     *
     * @param integer $row
     * @param mixed $column
     * @return mixed
     */
    function Exact($column = 0, $row = 0, $query = '') {
        try {
            $result = $this->Call($query);
            
            if (empty($result)) {return null;}

            if (is_numeric($column)) {
                $r_keys = array_keys($result[0]);
                $column = $r_keys[$column];
            }

            // isset is an implicit test against nonexistant row or associative column
            if (!isset($result[0][$column]) || count($result) - 1 < $row) {
                return NULL;
            } else {
                return $result[0][$column];
            }
        } catch (\Exception $th) {
            throw $th;
        }
    }

    /**
     * query presumes :[tag] syntax for parameterized queries
     * Note: PDO does not natively support IN calls fed as arrays in a single parameter
     *
     * @return array
     */
    function Call($query = '') {
        try {
            if (empty($query) && empty($this->_query)) {
                throw new \Exception("Null query presented", 1);
            }

            if ($this->dirty || ($this->statement instanceof PDOStatement) == false || !empty($query)) {
                if (!empty($query)) { $this->_query = $query;}
                $this->statement = $this->Prepare($this->_query);
            }
            
            if (!empty($this->params) && is_iterable($this->params) && reset($this->params) instanceof PDO_Parameter) {
                //debug_line("Params not empty? " . var_export(!empty($this->params),true));
                //debug_line("Params not empty? " . var_export(is_iterable($this->params),true));
                //debug_line("Params not empty? " . var_export(reset($this->params) instanceof PDO_Parameter),true);
                $this->Bind();
            }

            $this->statement->execute();

            if ($this->statement->columnCount() > 0) {
                return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return $this->statement->rowCount();
            }

            
        } catch (\Exception $th) {
            throw $th;
        }
    }

    /**
     * prepared statement prep and processing
     *
     * @param mixed $query
     * @todo parse arrays fed into a variable and explode some marker. However this does mean the statment can't be reused unless the variables are exact-count.
     * @return PDOStatement
     */
    function Prepare($query, $autofill = true) {
        global $link_pdo;
        if (is_array($query)) {
            $query = implode(PHP_EOL,$query); 
        }

        if ($autofill) {
            $this->statement = ($query instanceof PDOStatement) ? $query : $this->dblink->prepare($query);
        }
        
        return $this->statement;
    }

    function Bind() {
        foreach ((array) $this->params as $key => $parameter) {
            if (is_scalar($parameter)) {
                $random_tag = substr(sha1(time()), 0, 10);
                $this->params[$key] = new PDO_Parameter($random_tag,strval($parameter),\PDO::PARAM_STR);
            }

            $this->params[$key]->bind($this->statement);
        }
    }
}

class PDO_Parameter {
    /**
     * tag used in SQL string
     *
     * @var string
     */
    public $tag = '';
    
    /**
     * value used for parameter, can be null
     *
     * @var mixed
     */
    public $value = null;

    /**
     * whether the item is iterable or otherwise not scalar. read only.
     *
     * @var boolean
     */
    public $is_compound = false;

    /**
     * Flat and clean array for binding
     *
     * @var array
     */
    protected $value_flat = [];

    /**
     * type of the variable or all its members, may not be an array.
     *
     * @var int
     */
    public $type = \PDO::PARAM_STR;

    /**
     * Create a pdo parameter, use /pdo::PARAM_ types. floats are to be treated as strings in pdo. 
     * Accepts arrays (for IN qualifiers) but they must be the same type, not null. If integer type also not ''.
     * If floats are specified in a compound parameter then the values must be strings.
     *
     * @param string $tag
     * @param mixed $value
     * @param int $type
     */
    function __construct($tag, $value, $type = \PDO::PARAM_STR) {
        $this->tag = preg_replace('/\s+/', '', strval($tag));
        $this->value = (!is_null($value)) ? $value : $this->default_by_type($type);
        $this->value_flat = (!empty($value) && is_iterable($value)) ? array_flatten($value) : [];
        $this->is_compound = !is_null($value) && !is_scalar($value) && is_iterable($value);
        $this->type = $type;
    }

    /**
     * return defaults for a given parameter type. floats are strings.
     *
     * @param integer $type
     * @return void
     */
    protected function default_by_type(&$type) {
        switch ($type) {
            case \pdo::PARAM_STR:
            case \pdo::PARAM_LOB:
                return '';
                break;
            case \pdo::PARAM_INT:
                return 0;
                break;
            case \pdo::PARAM_BOOL:
                return FALSE;
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * produce sequentially named list of variables according to input value object and append values to the end of the variable list
     * CAVEAT: All multi-dimensional values will be arbitarily flattened then controlled for uniqueness.
     *
     * @return void
     */
    protected function inlist() {
        $valuelist = [];

        if (is_null($this->value) || (is_scalar($this->value) && !is_iterable($this->value))) {
            return array($this->tag);
        }

        if (is_iterable($this->value)) {
            $flat_values = array_flatten($this->value);
            foreach ($flat_values as $item) {
                //nulls cannot be compared for an IN list. skip them.
                if (is_null($item)) { continue; }
                
                if(is_scalar($item)) {
                    $valuelist[] = $item;
                } else {
                    $caller = get_caller(__FILE__);
                    error_log("{$caller['file']} {$caller['function']}:{$caller['line']} - WARNING: PDO parameter ({$this->tag}) recieved a non-scalar inside a child member. This cannot be made valid for an IN list and therefore the value was ignored.");
                }
            }
        }
        //values might as well be unique for the IN list.
        //if for nothing else than preventing accidental mass-duplication that might clog something in PDO
        return array_unique($valuelist);
    }

    /**
     * returns a parenthesis wrapped sequential tag list for use in an IN statement to bind parameters with.
     * for now it'll have to be manually added by code to the sql statement.
     *
     * @return string
     * @todo reliable string replacement pre-prepare
     */
    function in_string() {
        $keys = [];
        
        if (!empty($this->value) && empty($this->value_flat)) {
            $this->value_flat = array_flatten($this->value);
        }

        foreach ($this->value_flat as $key => $value) {
            //while in theory you could pass a assoc feed from value_flat, don't do that. it will result in duplicate tags
            $keys[] = $this->tag . $key; 
        }
        $qualifier = '(' . implode(', ', $keys) . ')';

        return $qualifier;
    }

    /**
     * bind parameter value to statement
     *
     * @param PDOStatement $stmt
     * @return void
     */
    function bind(&$stmt) {
        if (!$this->is_compound) {
            $stmt->bindValue(':' . $this->tag, $this->value);
        } else {
            // in list processing would go here
        }
    }
}
}
