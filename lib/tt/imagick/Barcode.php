<?php
namespace tt\imagick;
/**
 * バーコード
 * @author tokushima
 *
 */
class Barcode extends \ebi\Barcode{
	
	/**
	 * Imageで返す
	 * @param array $opt
	 * 
	 * opt:
	 * 	string $color #000000
	 * 	number $bar_height バーコードの高さ
	 * 	number $module_width 1モジュールの幅
	 * 
	 * @return \tt\Imagick
	 */
	public function image($opt=[]){
		$this->setopt($opt);
		
		$w = 0;
		foreach($this->data as $d){
			foreach($d as $bw){
				$w += ($bw < 0) ? ($bw * -1) * $this->module_width : ($bw * $this->module_width);
			}
		}
		
		
		$x = 0;
		$image = \tt\Imagick::create($w, $this->bar_height);
		foreach($this->data as $i => $d){
			foreach($d as $j => $bw){
				if($bw < 0){
					$x += ($bw * -1) * $this->module_width;
				}else{
					list($y,$h) = $this->bar_type($i,$j);
					$image->rectangle($x, $y, ($bw * $this->module_width), $h, $this->color,0,true);
					$x += ($bw * $this->module_width);
				}
			}
		}
		return $image;
	}
	

}
