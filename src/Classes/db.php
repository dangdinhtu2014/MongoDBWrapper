<?php

class Db
{
    protected static $__instance;
    private  $__connectionName;
    private $__settings;
    private $__connected;
    private $_collection;
    private $_mng;

    public function __construct($settings=null)
    {
        /**
         * Set Connection Name
         *
         */

        $this->__connectionName = $settings['connectionName'];

        /**
         * Check that Settings array has been passed.
         * If it is not an array, or the array is empty
         * Set the settings to false;
         *
         */
            is_array($settings) && !empty($settings) ? $this->setSettings($settings) : $this->setSettings(boolval(0));

        /**
         * Check if the instance has been set. If not
         * then set the instance to reference of $this;
         *
         */
             if(!isset(self::$__instance[$settings['connectionName']]))
             {
                self::$__instance[$settings['connectionName']] = &$this;
             }

         /**
          * Connect to the Database;
          *
          */

            $this->connect();

            if($this->__connected===true){
                return true;
            }
            else{
                return false;
            }
    }

    public function connect()
    {
        if($this->__connected) return true;

        /**
         * Check that the port is defined in settings.
         * If the port has not been defined, then set
         * the port to the default MongoDB connection
         * port;
         *
         */
            isset($this->__settings['port']) && $this->__settings['port'] ? $port = $this->__settings['port'] : $port = '27017';

        try {

            /**
             * Construct Connection String;
             *
             */
                $connection_string = "mongodb://" . $this->__settings['host'] . ':' . $port;

            /**
             * Create Connection to the MongoDB
             * Database;
             *
             */
                $this->_mng = new MongoDB\Driver\Manager($connection_string, array($this->__settings['user'] , $this->__settings['pass'] ));
                $this->__connected = true;

            return $this->__connected;
        }
        catch (MongoDB\Driver\Exception\Exception $e) {

            /**
             * Get the name of the file which called the script;
             *
             */
                $filename = basename(__FILE__);

            /**
             * Echo back the error results of the
             * connection to MongoDB;
             */
                echo "The $filename script has experienced an error.\n";
                echo "It failed with the following exception:\n";

                echo "Exception:", $e->getMessage(), "\n";
                echo "In file:", $e->getFile(), "\n";
                echo "On line:", $e->getLine(), "\n";
        }

    }

    public function setSettings($settings)
    {
        /**
         * If Settings are not defined, return to
         * call with error message;
         *
         */
            if($settings===false){
                return "No Settings Defined.";
            }
            else {

                /**
                 * Else set the encapsulated $__settings
                 * variablewith the contents of the $settings
                 * array passed into the function;
                 *
                 */

                    $this->__settings = $settings;

                return true;
            }
    }

    public function getConnection()
    {
        return $this->__connection;
    }

    /**
     * @param $query
     */

    public function interact($query)
    {
        switch($query['type']){
            case 'insert' :
                try{
                    $data = $query['data'];
                    $ins = new MongoDB\Driver\BulkWrite;

                    /**
                     * Create an Insert for each piece of data;
                     *
                     */

                        foreach($data as $data_row)
                        {
                            $ins->insert($data_row);
                        }

                    /**
                     * Execute the insert;
                     *
                     */

                        $this->_mng->executeBulkWrite($query['collection'], $ins);
                }
                catch (MongoDB\Driver\Exception\Exception $e) {

                    /**
                     * Get the name of the file which called the script;
                     *
                     */
                        $filename = basename(__FILE__);

                    /**
                     * Echo back the error results of the
                     * connection to MongoDB;
                     */
                        echo "The $filename script has experienced an error.\n";
                        echo "It failed with the following exception:\n";

                        echo "Exception:", $e->getMessage(), "\n";
                        echo "In file:", $e->getFile(), "\n";
                        echo "On line:", $e->getLine(), "\n";
                }
            break;
            case 'remove':
                try{
                    $data = $query['data'];
                    $del = new MongoDB\Driver\BulkWrite;

                    /**
                     * Apply the Remove Query array to the
                     * BulkWrite variable;
                     *
                     */
                        $del->delete($data);

                    /**
                     * Execute the Remove;
                     *
                     */

                    $this->_mng->executeBulkWrite($query['collection'], $del);
                }
                catch (MongoDB\Driver\Exception\Exception $e) {

                    /**
                     * Get the name of the file which called the script;
                     *
                     */
                    $filename = basename(__FILE__);

                    /**
                     * Echo back the error results of the
                     * connection to MongoDB;
                     */
                    echo "The $filename script has experienced an error.\n";
                    echo "It failed with the following exception:\n";

                    echo "Exception:", $e->getMessage(), "\n";
                    echo "In file:", $e->getFile(), "\n";
                    echo "On line:", $e->getLine(), "\n";
                }
            break;
        }
    }

    public function queryAll($collection)
    {
        try {

            $query = new MongoDB\Driver\Query([]);

            $rows = $this->_mng->executeQuery($collection, $query);

            $data = array();
            foreach ( $rows as $row )
            {
                $data[] = $row;
            }

            return $data;

        } catch (MongoDB\Driver\Exception\Exception $e) {

            $filename = basename(__FILE__);

            echo "The $filename script has experienced an error.\n";
            echo "It failed with the following exception:\n";

            echo "Exception:", $e->getMessage(), "\n";
            echo "In file:", $e->getFile(), "\n";
            echo "On line:", $e->getLine(), "\n";
        }

    }

    public function query($collection, $filter, $options)
    {
        try {

            $query = new MongoDB\Driver\Query($filter,$options);

            $rows = $this->_mng->executeQuery($collection, $query);


            $data = array();
            foreach ( $rows as $row )
            {
                $data[] = $row;
            }

            return $data;

        } catch (MongoDB\Driver\Exception\Exception $e) {

            $filename = basename(__FILE__);

            echo "The $filename script has experienced an error.\n";
            echo "It failed with the following exception:\n";

            echo "Exception:", $e->getMessage(), "\n";
            echo "In file:", $e->getFile(), "\n";
            echo "On line:", $e->getLine(), "\n";
        }
    }
}