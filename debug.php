<?php
	function dbg(){
		$str = '';
		$backtrace = debug_backtrace();
		
		$str .= '<div style="text-align:left">';
		$args = func_get_args();
		$str .= '<h1>'.$backtrace[1]['class'].'::'.$backtrace[1]['func'].' line '.$backtrace[0]['line'].'</h1>';
		foreach($args as $k=>$arg){
			$str .= '<h2>Arg '.$k.'</h2><pre>';
			$str.= print_r($arg,true);
			$str.= '</pre><hr>';
		}
		$str .= '</div>';
		echo $str;
	}
	
?>
