<?php
class parameters extends randomAccess {

	function __construct(){
		parent::__construct();
		$this->params = $_POST;
	}

}

class randomAccess {
	protected $params;

	function __construct(){
		$this->params = array();
	}

	function __set( $name, $value ){
		$this->params[ $name ] = $value;
	}

	function __get( $name ){
		$result = "";
		if( array_key_exists( $name, $this->params ) ){
			$result = $this->params[ $name ];
		}
		else if( array_key_exists( $name, $_POST ) ){
			$result = $_POST[ $name ];
		}
		else if( array_key_exists( $name, $_GET ) ){
			$result = $_GET[ $name ];
		}
		return $result;
	}
}


?>
