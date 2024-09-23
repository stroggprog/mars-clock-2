<?php
/* 
  SQLite3 database class.

  WARNING:

  Unlike other SQL implementations, SQLite follows these concventions in SQL query strings:

  Double-quotes: are ALWAYS used to delimit column names
  Single-quotes: are ALWAYS used to delimit values

  If you double-quote a value, it will be interpreted as a column name and vice-versa.
*/

function SQLescapeString( $string = "" ){
  static $sqlObj = false;
  if( !$sqlObj ){
    $sqlObj = new SQLite3(':memory:');
  }
  return $sqlObj->escapeString( $string );
}

class DB_Sql {
  
  /* public: connection parameters */

  var $Filename = "";
  var $Flags = 0;
  var $EncryptionKey = "";

  /* public: configuration parameters */
  var $Auto_Free     = 0;     ## Set to 1 for automatic mysql_free_result()
  var $Debug         = 0;     ## Set to 1 for debugging messages.
  var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  var $Seq_Table     = "db_sequence";

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "sqlite3";
  var $revision = "1.0";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;
  
  /* public: constructor */
  function __constructor( $fname = "", $flags = 0, $encKey = "" ){
    return $this;
  }

  /* public: some trivial reporting */
  function link_id() {
    return $this->Link_ID;
  }

  function query_id() {
    return $this->Query_ID;
  }

  function disconnect(){
    $this->Link_ID->close();
  }

  function connect( $Filename = "", $Flags = 0, $EncryptionKey = "" ){
    if( $Filename == "" ) $Filename = $this->Filename;
    if( $Flags == 0 ) $Flags = $this->Flags;
    if( $Flags == 0 ) $Flags = (SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    if( $EncryptionKey == "" ) $EncryptionKey = $this->EncryptionKey;

    /*
    if( file_exists( $Filename ) ){
      echo "found $Filename<br>";
    }
    else if( file_exists( "Data\$Filename") ){
      echo "found Data\$Filename";
    }
    else if( file_exists( "\Data\$Filename") ){
      echo "found \\Data\\$Filename";
    }
    */
    if( !is_object($this->Link_ID) ){
      $this->Link_ID = new SQLite3( "$Filename", $Flags, $EncryptionKey );
      if (!$this->Link_ID) {
        $this->halt("connect($Filename) failed.");
        return 0;
      }
    }
    return $this->Link_ID;
  }


  /* public: discard the query result */
  function free() {
    if( $this->Query_ID ){
      $this->Query_ID->finalize();
      $this->Query_ID = 0;
    }
  }

  /* public: perform a query */
  function query($Query_String) {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;

    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }

    if ($this->Debug)
      printf("Debug: query = %s<br>\n", $Query_String);

    $this->Query_ID = $this->Link_ID->query( $Query_String );
    $this->Row   = 0;
    $this->Errno = $this->Link_ID->lastErrorCode();
    $this->Error = $this->Link_ID->lastErrorMsg();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: ".$Query_String);
    }

    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }

  function execute( $query_String ){
	  $this->query( $query_String );
	  return $this->next_record();
  }
  
  function insert_id()
  {
    return $this->Link_ID->lastInsertRowID();
  }


  /* public: walk result set */
  function next_record() {
    if( !is_object($this->Query_ID) ){
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Record = $this->Query_ID->fetchArray( SQLITE3_ASSOC );
    $this->Errno = $this->Link_ID->lastErrorCode();
    $this->Error = $this->Link_ID->lastErrorMsg();

    if( $this->Record === false ){
      return false;
    }

    $stat = false;
    if( $this->Query_ID->numColumns() && $this->Query_ID->columnType(0) != SQLITE3_NULL ){
      $this->Row++;
      $stat = true;
    }
    else {
      if( $this->Auto_Free ) $this->free();
    }
    
    return $stat;
  }

  function locate( $col, $value ){
  	$a = 0;
  	while( $this->next_record() ){
  		$a++;
  		if( $this->f($col) == $value ){
  			return $a;
  		}
  	}
  	return 0;
  }

  function as_obj(){
    return json_decode( json_encode( $this->Record ) );
  }

  function next_rec_as_obj(){
    if( $this->next_record() ){
      return $this->as_obj();
    }
    return 0;
  }

  /* public: evaluate the result (size, width) */
  function affected_rows() {
    return $this->Link_ID->changes();
  }

  function num_rows(){
    if( !$this->Query_ID ) return 0;
    $oldRow = $this->Row;
    $this->Query_ID->reset();
    $rows = 0;
    while( $this->Query_ID->fetchArray() ) $rows++;
    $this->Query_ID->reset();
    $crow = 0;
    while( $crow < $oldRow ){
      $this->Query_ID->fetchArray();
      $crow++;
    }
    return $rows;
  }

  function num_fields() {
    if( $this->Query_ID ){
      return $this->Query_ID->numColumns();
    }
    return 0;
  }

  function record() {
    if( $this->Query_ID ){
      return $this->Record;
    }
    return fale;
  }
  function f($Name) {
    if(isset($this->Record[$Name]))
      return $this->Record[$Name];
    else 
      return "";
  }

  function af( $flist ){
    $r = array();
    foreach( $flist as $fname ){
      $r[$fname] = $this->Record[$fname];
    }
    return $r;
  }

  function recdata(){
    return $this->Record;
  }

  function p($Name) {
    print $this->Record[$Name];
  }


  /* private: error handling */
  function halt($msg) {
    if ($this->Halt_On_Error == "no")
      return;

    $this->haltmsg($msg);
    $this->sqlerror($msg);
    
    if ($this->Halt_On_Error != "report")
      die("<br>Session halted.");
  }

  function haltmsg($msg) {
    printf("<br>Oh dear, we got an error");
    /*
    printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
    printf("<b>MySQL Error</b>: %s (%s)<br>\n",
      $this->Errno,
      $this->Error);
    */
  }

  function table_names() {
    $this->query("SHOW TABLES");
    $i=0;
    while ($info=mysql_fetch_row($this->Query_ID))
     {
      $return[$i]["table_name"]= $info[0];
      $return[$i]["tablespace_name"]=$this->Database;
      $return[$i]["database"]=$this->Database;
      $i++;
     }
   return $return;
  }

	function sqlerror( $data ){
		$en = $this->Errno;
		$em = $this->Error;
		// $msg = "Database error: $data\nMySQL Error: $en ($em)";
		file_put_contents('sql_error', "$data\n", FILE_APPEND );
	}

}

?>