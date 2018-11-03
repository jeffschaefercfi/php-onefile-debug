<?php
class Debug{
	
	static function dbg(){
		$str = '';
		$args = func_get_args();
		foreach($args as $k=>$arg){
			$str .= '<h2>Debug Param '.$k.'</h2><pre>';
			$str.= print_r($arg,true);
			$str.= '</pre><hr>';
		}
		$str .= '<hr>';
		echo $str;
		
	}
	
}
?>
