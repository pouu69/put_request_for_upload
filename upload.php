<?php


   	function _put_save_for_upload(){
		$tmp_dir = "C:\wamp\www\Temp";

	    $put_data = file_get_contents('php://input');

	    $a_data = array();

	    preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
	    $boundary = $matches[1];

	    $a_blocks = preg_split("/-+$boundary/", $put_data);
	    array_pop($a_blocks);

	    foreach ($a_blocks as $id => $block)
	    {
			if (empty($block))
				continue;

	        preg_match('/name=\"([^\"]*)\".*filename=\"([^\"]+)\"[\n\r]+Content-Type:\s+([^\s]*?)[\n\r]+?([^\n\r].*?)\r$/Us', $block, $matches);

	        if (count($matches)==0){
	          preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
	          $a_data[$matches[1]] = $matches[2];
	        } else {
				$tmp_name = tempnam($tmp_dir,'');
				file_put_contents($tmp_name, $matches[4]);
				$size = filesize($tmp_name);

				preg_match('/([a-zA-Z_0-9]+)/s', $matches[1], $name);
				preg_match_all('/\[([a-zA-Z_0-9]*)\]/s', $matches[1], $arr);

				$file = array(
					'name'=>null,
					'type'=>null,
					'tmp_name'=>null,
					'error'=>null,
					'size'=>null,
				);

				$arr = $arr[1];
				$name = $name[1];
				$args = array();

				foreach ($file as $key => &$value)
				{
					$args[]=&$value;
				}
				for ($i = 0; $i < count($arr); $i++)
				{
					for ($k = 0; $k < count($args); $k++)
					{
						$args[$k] = array();
						if ($arr[$i]==''){
							$args[$k][] = null;
							$x= count($args[$k])-1;
							$args[$k] = &$args[$k][$x];
						} else {
							$args[$k][$arr[$i]] = null;
							$args[$k] = &$args[$k][$arr[$i]];
						}
					}
				}

				$args[0] = $matches[2]; //filename
				$args[1] = $matches[3]; //type
				$args[2] = $tmp_name; //tmp_name
				$args[3] = 0; //error
				$args[4] = $size; //size
				$_FILES[$name] = $file;
			}
	    }
	}


	function _file_get_contents(){
		$dir_path = 'upload/';
		$img_path = $_FILES['files']['tmp_name'];
	    $type = explode('/', $_FILES['files']['type'])[1];

		$content = file_get_contents($img_path);
		if($content !== false){
	        $file_name = explode('.',end(explode('\\', $img_path)))[0];
	        file_put_contents($dir_path.$file_name.'.'.$type, $content);
		}
	}

	_put_save_for_upload();
	_file_get_contents();
?>
