<?php
/**
 * Magento Table Maintenance Script
 * Put this file in the root of the magento install and then in browser go to: 
 * http://DOMAIN.COM/cleanup.php?clean=log
 */


// Look at what action has been asked cleanup.php?clean=[command]
switch($_GET['clean']) {
    // If log has been requested, run the clean_log_tables() function
    case 'log':
        clean_log_tables();
    break;
}

// Begin clean_log_tables function
function clean_log_tables() {
    
    // Get the contents of the Magento config file for DB connection details and load them into the $xml variable array
    $xml = simplexml_load_file('./app/etc/local.xml', NULL, LIBXML_NOCDATA);
    
    // If there is data found 
    if(is_object($xml)) {
        // Create variables containing the username, password, database etc.. (If there is a table prefix, it will get this also)
        $db['host'] = $xml->global->resources->default_setup->connection->host;
        $db['name'] = $xml->global->resources->default_setup->connection->dbname;
        $db['user'] = $xml->global->resources->default_setup->connection->username;
        $db['pass'] = $xml->global->resources->default_setup->connection->password;
        $db['pref'] = $xml->global->resources->db->table_prefix;
        
        // What tables need to be cleared/truncated?
        $tables = array(
            'dataflow_batch_export',
            'dataflow_batch_import',
            'log_customer',
            'log_quote',
            'log_summary',
            'log_summary_type',
            'log_url',
            'log_url_info',
            'log_visitor',
            'log_visitor_info',
            'log_visitor_online',
            'report_event',
            'report_viewed_product_index',
            'report_compared_product_index'
        );
        
        // Connect to database, if there is a problem, it will display the error to you
        mysql_connect($db['host'], $db['user'], $db['pass']) or die(mysql_error());
        mysql_select_db($db['name']) or die(mysql_error());
        
        // Loop through the array of table names above and for each one, TRUNCATE it and then OPTIMIZE it.
        foreach($tables as $table) {
            @mysql_query('TRUNCATE `'.$db['pref'].$table.'`');
            @mysql_query('OPTIMIZE TABLE `'.$db['pref'].$table.'`');
            echo "Table ".$db['pref'].$table." Truncated and Optimized! <br />";
        }

    } else {

        // If script cannot find the local.xml file, display error.. If this is displayed, it most likely due to the script being put in the wrong folder.
        exit('Unable to locate and load local.xml file, make sure this file is in the root of the Magento install!');

    }
} // END FUNCTION

?>
