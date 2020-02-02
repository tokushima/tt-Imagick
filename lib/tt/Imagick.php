<?php
namespace tt;
/**
 * Imagick
 * 
 * Install:
 *  brew install imagemagick 
 *  brew install gs
 *  pecl install imagick
 * 
 * @author tokushima
 *
 */
class Imagick{
	/**
	 * 縦向き
	 * @var integer
	 */
	const ORIENTATION_PORTRAIT = 1;
	/**
	 * 横向き
	 * @var integer
	 */
	const ORIENTATION_LANDSCAPE = 2;
	/**
	 * 正方形
	 * @var integer
	 */
	const ORIENTATION_SQUARE = 3;
	
	private $canvas;
	
	public function __construct($filename){
		if($filename != __FILE__){
			$this->canvas = new \Imagick($filename);
		}
	}
	public function __destruct(){
		$this->canvas->clear();
	}
	
	/**
	 * バイナリ文字列から画像を読み込む
	 * @param string $string
	 * @return \tt\Imagick
	 */
	public static function read($string){
		$self = new static(__FILE__);
		$self->canvas = new \Imagick();
		
		if($self->canvas->readImageBlob($string) !== true){
			throw \InvalidArgumentException('invalid image');
		}
		return $self;
	}
	
	/**
	 * 塗りつぶした矩形を作成する
	 * @param integer $width
	 * @param integer $height
	 * @param string $color
	 * @param string $filename
	 * @return \tt\Imagick
	 */
	public static function create($width,$height,$color='#FFFFFF'){
		$self = new static(__FILE__);
		$self->canvas = new \Imagick();
		$self->canvas->newImage($width,$height,$color);
		
		return $self;
	}
	
	/**
	 * ファイルに書き出す
	 * @param string $filename
	 */
	public function write($filename){
		$this->canvas->writeImage($filename);
	}
	
	/**
	 * 画像を出力する
	 * @param string $format
	 */
	public function output($format='jpeg'){
		$format = strtolower($format);
		
		switch($format){
			case 'png':
				header('Content-Type: image/png');
				break;
			case 'gif':
				header('Content-Type: image/gif');
				break;
			default:
				header('Content-Type: image/jpeg');
				$format = 'jpeg';
		}
		$this->canvas->setImageFormat($format);
		print($this->canvas);
	}
	
	/**
	 * 画像を返す
	 * @param string $format
	 * @return string
	 */
	public function get($format='jpeg'){
		$format = strtolower($format);
		
		switch($format){
			case 'png':
				header('Content-Type: image/png');
				break;
			case 'gif':
				header('Content-Type: image/gif');
				break;
			default:
				header('Content-Type: image/jpeg');
				$format = 'jpeg';
		}
		$this->canvas->setImageFormat($format);
		return $this->canvas->getImageBlob();
	}
	
	
	/**
	 * 画像の一部を抽出する
	 * @param integer $width 抽出する幅
	 * @param integer $height 抽出する高さ
	 * @param integer $x 抽出する領域の左上の X 座標
	 * @param integer $y 抽出する領域の左上の Y 座標
	 * @return \tt\Imagick
	 */
	public function crop($width,$height,$x=null,$y=null){
		list($w,$h) = $this->get_size();
		
		if($width >= $w && $height >= $h){
			return $this;
		}
		
		if($x === null || $y === null){
			$x = ($w - $width) / 2;
			$y = ($h - $height) / 2;
			
			list($x,$y) = [($x >= 0) ? $x : 0,($y >= 0) ? $y : 0];
		}
		if($x < 0){
			$x = $w + $x;
		}
		if($y < 0){
			$y = $h + $y;
		}
		$this->canvas->cropImage($width,$height,$x,$y);
		
		return $this;
	}
	
	/**
	 * 画像のサイズを変更する
	 * @param integer $width 変更後の幅
	 * @param integer $height 変更後の高さ
	 * @return \tt\Imagick
	 */
	public function resize($width,$height=null){
		list($w,$h) = $this->get_size();
		$rw = empty($width) ? 1 : $width;
		$rh = empty($height) ? 1 : $height;
		
		if(!empty($width) && !empty($height)){
			$aw = $rw / $w;
			$ah = $rh / $h;
			$a = max($aw,$ah);
		}else if(!isset($height)){
			$a = $rw / $w;
		}else{
			$a = $rh / $h;
		}
		$cw = $w * $a;
		$ch = $h * $a;
		
		$this->canvas->scaleImage($cw,$ch);
		
		return $this;
	}
	
	/**
	 * 指定した幅と高さに合うようにリサイズとトリミングをする
	 * @param integer $width
	 * @param integer $height
	 */
	public function crop_resize($width,$height){
		$this->resize($width,$height)->crop($width, $height);
		
		return $this;
	}
	
	/**
	 * 回転
	 * @param integer $angle 角度
	 * @param string $background_color
	 * @return \tt\Imagick
	 */
	public function rotate($angle,$background_color='#000000'){
		$this->canvas->rotateImage($background_color,$angle);
		
		return $this;
	}
	
	/**
	 * マージ
	 * @param integer $x
	 * @param integer $y
	 * @param \tt\Imagick $img
	 * @param integer $composite imagick::COMPOSITE_*
	 * @return \tt\Imagick
	 * @see https://www.php.net/manual/ja/imagick.constants.php
	 */
	public function merge($x,$y,\tt\Imagick $img,$composite=\Imagick::COMPOSITE_OVER){
		$this->canvas->compositeImage(
			$img->canvas,
			$composite,
			$x,
			$y
		);
		return $this;
	}
	
	
	/**
	 * サイズ
	 * @return integer[]
	 */
	public function get_size(){
		$w = $this->canvas->getImageWidth();
		$h = $this->canvas->getImageHeight();
		
		return [$w,$h];
	}
	
	/**
	 * 画像の向き
	 * @return  integer
	 */
	public function get_orientation(){
		list($w,$h) = $this->get_size();
		
		$d = $h / $w;
		
		if($d <= 1.02 && $d >= 0.98){
			return self::ORIENTATION_SQUARE;
		}else if($d > 1){
			return self::ORIENTATION_PORTRAIT;
		}else{
			return self::ORIENTATION_LANDSCAPE;
		}
	}
	
	/**
	 * オプションを設定する
	 * @param string $k
	 * @param mixed $v
	 * @return \tt\Imagick
	 * @see https://www.php.net/manual/ja/imagick.setoption.php
	 */
	public function set_option($k,$v){
		$this->canvas->setOption($k,$v);
		return $this;
	}
	
	/**
	 * 差分の抽出
	 * @param \tt\Imagick $image
	 * @return \tt\Imagick
	 */
	public function diff(\tt\Imagick $image){
		$result = $this->canvas->compareImages($image->canvas, \Imagick::METRIC_MEANSQUAREERROR);
		
		$diff = new static(__FILE__);
		$diff->canvas = $result[0];
		
		return $diff;
	}
}