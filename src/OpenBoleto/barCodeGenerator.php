<?php

/*
* OpenBoleto - Geração de boletos bancários em PHP
*
* LICENSE: The MIT License (MIT)
*
* Copyright (C) 2013 Estrada Virtual
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of this
* software and associated documentation files (the "Software"), to deal in the Software
* without restriction, including without limitation the rights to use, copy, modify,
* merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
* permit persons to whom the Software is furnished to do so, subject to the following
* conditions:
*
* The above copyright notice and this permission notice shall be included in all copies
* or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
* INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
* PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
* OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
* SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*
* Classe copiada de http://www.phpclasses.org/browse/file/28826.html de autoria de Raj Kumar Trivedi e adaptada para
* o projeto openBoleto por Rogerio Muniz de Castro rogerio@ciatec.net
*/

namespace OpenBoleto;
class barCodeGenerator{ 
	private $file; 
	private $into; 
	private $data64;
	private $img;
	private $digitArray = array(0=>"00110",1=>"10001",2=>"01001",3=>"11000",4=>"00101",5=>"10100",6=>"01100",7=>"00011",8=>"10010",9=>"01010"); 
	function __construct($value,$into=1) { 
		$lower = 1 ; $hight = 50; 
		$this->into = $into; 
		for($count1=9;$count1>=0;$count1--){ 
			for($count2=9;$count2>=0;$count2--){ 
				$count = ($count1 * 10) + $count2 ; 
				$text = "" ; 
				for($i=1;$i<6;$i++){ 
					$text .= substr($this->digitArray[$count1],($i-1),1) . substr($this->digitArray[$count2],($i-1),1); 
				} 
				$this->digitArray[$count] = $text; 
			} 
		} 
		$this->img = imagecreatetruecolor(450,50); 
		imagesavealpha($this->img, true);
		$trans_colour = imagecolorallocatealpha($this->img, 0, 0, 0, 127);
		imagefill($this->img, 0, 0, $trans_colour);
		$cl_black = imagecolorallocate($this->img, 0, 0, 0); 
		$cl_white = imagecolorallocate($this->img, 255, 255, 255); 

		imagefilledrectangle($this->img, 1,5,1,65,$cl_black); 
		//imagefilledrectangle($this->img, 2,5,2,65,$cl_white); 
		imagefilledrectangle($this->img, 3,5,3,65,$cl_black); 
		//imagefilledrectangle($this->img, 4,5,4,65,$cl_white); 
		$thin = 1 ; 
		if(substr_count(strtoupper($_SERVER['SERVER_SOFTWARE']),"WIN32")){ 
			$wide = 3; 
		} else { 
			$wide = 2.72; 
		} 
		$pos = 5 ; 
		$text = $value ; 
		if((strlen($text) % 2) <> 0){ 
			$text = "0" . $text; 
		} 
		while (strlen($text) > 0) { 
			$i = round($this->JSK_left($text,2)); 
			$text = $this->JSK_right($text,strlen($text)-2); 

			$f = $this->digitArray[$i]; 

			for($i=1;$i<11;$i+=2){ 
				if (substr($f,($i-1),1) == "0") { 
					$f1 = $thin ; 
				}else{ 
					$f1 = $wide ; 
				} 
				imagefilledrectangle($this->img, $pos,5,$pos-1+$f1,65,$cl_black) ; 
				$pos = $pos + $f1 ; 

				if (substr($f,$i,1) == "0") { 
					$f2 = $thin ; 
				}else{ 
					$f2 = $wide ; 
				} 
				//imagefilledrectangle($this->img, $pos,5,$pos-1+$f2,65,$cl_white) ; 
				$pos = $pos + $f2 ; 
			} 
		} 
		imagefilledrectangle($this->img, $pos,5,$pos-1+$wide,65,$cl_black); 
		$pos=$pos+$wide; 

		//imagefilledrectangle($this->img, $pos,5,$pos-1+$thin,65,$cl_white); 
		$pos=$pos+$thin; 


		imagefilledrectangle($this->img, $pos,5,$pos-1+$thin,65,$cl_black); 
		$pos=$pos+$thin; 
	} 

	function JSK_left($input,$comp){ 
		return substr($input,0,$comp); 
	} 

	function JSK_right($input,$comp){ 
		return substr($input,strlen($input)-$comp,$comp); 
	} 
	function get_data64(){
		ob_start(); // Let's start output buffering.
		imagepng($this->img); //This will normally output the image, but because of ob_start(), it won't.
		$contents = ob_get_contents(); //Instead, output above is saved to $contents
		ob_end_clean();
		return "data:image/png;base64," . base64_encode($contents);
		imagedestroy($this->img);
	}
	function get_img(){
		return $this->img;
		imagedestroy($this->img);
	}
} 

?> 