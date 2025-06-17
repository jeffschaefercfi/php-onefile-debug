<?php
	
	static function dbg(){
		$str = '';
		$num = getmypid();//used in js to keep console vars separate
		++self::$dbg_instances;
		$args = func_get_args();
		$bt = debug_backtrace();
		$filename = $bt[0]['file'];
		$line = $bt[0]['line'];
		$arg_idx = self::get_arg_index($line,$filename);
		if(self::$dbg_instances == 1){
			$str .= self::get_dbg_css();
		}
		$str .= '<div class="debug-outer">';
		$str .= '	<div class="debug-header">';
		$str .= '		<h1>Debug '.self::$dbg_instances.'</h1>';
		$inst = self::$dbg_instances;//store inst for javascript
		$str .= '		<h2>'.$filename.' Line '.$line.'</h2>';
		$start_time = $GLOBALS['start_time']??microtime(1);
		$now = microtime(1);
		$time_elapsed = round($now - $start_time,4);
		$mem = memory_get_usage(1);
		$peakmemory = memory_get_peak_usage(1);
		$elapsed = 'Elapsed: '.$time_elapsed.' | Memory: '.$mem.' | Peak Memory: '.$peakmemory;
		$str .= '		<h2>'.$elapsed.'</h2>';
		$str .= '	</div>';
		foreach($args as $k=>$arg){
			$argname = $arg_idx[$k];//name of arg
			$argtype = gettype($arg);
			$argvalue = print_r($arg,true);
			if($argtype == 'array'){
				$argtype .= ' | Count: '.count($arg);
			}elseif($argtype == 'string'){
				$argtype .= ' | Length: '.strlen($arg);
			}
			$str .= '	<div class="debug-argouter">';
			$str .= '	<div class="debug-arghead">';
			$str .= '<strong>';
			$str .= $argname;
			$str .= '</strong> | Type: ';
			$str .= $argtype;
			$str .= '	</div>';
			$str .= '	<div class="debug-argbody">';
			$str .= '<pre>';
			$str .= $argvalue;
			$str .= '</pre>';
			$str .= '	</div>';
			$str .= '	</div>';
			
			++$num;//used to separate multiple javascript console logs
			$str .= <<<HTML
<script>
var debugstr$num = `Debug $inst $elapsed | Parameter: $argname | Parameter Type: $argtype
--Parameter Value--
$argvalue `;
console.log(debugstr$num);
</script>
HTML;

		}
		
		
		$str .= '</div>';
		
		echo $str;
		return;
	}
	
	
	static function consoledbg(){// a better output format for reading this on the command line
		$str = '';
		++self::$dbg_instances;
		$args = func_get_args();
		$bt = debug_backtrace();
		$filename = $bt[0]['file'];
		$line = $bt[0]['line'];
		$arg_idx = self::get_arg_index($line,$filename,'debug::consoledbg');

		$str .= '==========
Debug '.self::$dbg_instances;
		$str .= ' '.$filename.' Line '.$line.'
   ';
		$start_time = $GLOBALS['start_time']??microtime(1);
		$now = microtime(1);
		$time_elapsed = round($now - $start_time,4);
		$mem = memory_get_usage(1);
		$peakmemory = memory_get_peak_usage(1);
		$elapsed = 'Elapsed: '.$time_elapsed.' | Memory: '.$mem.' | Peak Memory: '.$peakmemory;
		$str .= $elapsed;

		foreach($args as $k=>$arg){
			$argname = $arg_idx[$k];//name of arg
			$argtype = gettype($arg);
			$argvalue = print_r($arg,true);
			if($argtype == 'array'){
				$argtype .= ' | Count: '.count($arg);
			}elseif($argtype == 'string'){
				$argtype .= ' | Length: '.strlen($arg);
			}

			$str .= '
   ';
			$str .= $argname;
			$str .= ' | Type: ';
			$str .= $argtype;
			$str .= '
   ';
			$str .= $argvalue;
			$str .= '
==========
';


		}

		echo $str;
		return;
	}
	
	
		static function get_dbg_css(){
		return <<<HTML
<style>
.debug-outer{
border: solid 1px;
border-radius: 10px;
margin-top:30px;
background-color: #FFFFFF;
}
.debug-header{

}
.debug-argouter{
margin:15px;
padding: 15px;
border: dashed 2px;
}
.debug-argheader{
margin-top: 10px;
background-color: #EAEAEA;
margin-left: 10px;
}
.debug-argbody{
padding: 10px;
font-size 14px;
}
</style>
HTML;
	}

	static function get_arg_index($line,$abs_file,$dbgfuncname='debug::dbg'){
		$line = $line - 1;
		$content = file_get_contents($abs_file);
		$lines = explode(PHP_EOL,$content);
		$dbgline = $lines[$line];
		$dbgpos = strpos(strtolower($dbgline),strtolower($dbgfuncname));
		$start_paren_passed = 0;//did we get passed the paren that starts the list of arguments
		$open_parens = 0;//how many parens deep are we in nesting?
		$arg_index_collector = array();
		$curr_arg = '';//collect characters in here
		foreach(str_split($dbgline) as $k=>$chr){
			if($k < $dbgpos){continue;}//start working after you get to the function name
			if($start_paren_passed){//looking for end
				if($chr == '('){++$open_parens;}//we found another paren
				elseif($chr == ')'){
					$open_parens = $open_parens - 1;
					if($open_parens == 0){//found the end
						$arg_index_collector[] = $curr_arg;//close out the last arg
						$curr_arg = '';//reset it just for my ocd
						break;//all done
					}
				}elseif($chr == ','){//this might be the border to a new arg
					if($open_parens == 1){//we are in the arg paren and not nested
						$arg_index_collector[] = $curr_arg;//end this arg and init the next one
						$curr_arg = '';//reset current argument
						$chr = '';//this is so the comma doesn't get added to $curr_arg below and then become part of the next arg
					}
				}
				$curr_arg .= $chr;
			}else{//looking for start
				if($chr == '('){
					$start_paren_passed = 1;
					$open_parens = 1;
				}else{continue;}
			}
		}
		return $arg_index_collector;
	}
	
	
?>
