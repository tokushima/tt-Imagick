<?php
namespace tt\image;
/**
 * Imagick
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
	
	private static $font_path = [];
	private $image;
	
	public function __construct($filename){
		if($filename != __FILE__){
			$this->image = new \Imagick($filename);
		}
	}
	public function __destruct(){
		$this->image->clear();
	}
	
	/**
	 * バイナリ文字列から画像を読み込む
	 * @param string $string
	 * @return \tt\image\Imagick
	 */
	public static function read($string){
		$self = new static(__FILE__);
		$self->image = new \Imagick();
		
		if($self->image->readImageBlob($string) !== true){
			throw new \tt\image\exception\ImageException('invalid image');
		}
		return $self;
	}
	
	/**
	 * 塗りつぶした矩形を作成する
	 * @param integer $width
	 * @param integer $height
	 * @param string $color
	 * @param string $filename
	 * @return \tt\image\Imagick
	 */
	public static function create($width,$height,$color=null){
		$self = new static(__FILE__);
		$self->image = new \Imagick();
		
		if(empty($color)){
			$color = '#FFFFFF';
		}

		try{
			$self->image->newImage($width,$height,$color);
		}catch (\ImagickException $e){
			throw new \tt\image\exception\ImageException();
		}
		return $self;
	}
	
	/**
	 * @return \Imagick
	 */
	public function image(){
		return $this->image;
	}

	/**
	 * ファイルに書き出す
	 * @param string $filename
	 * @param string png, gif, jpeg
	 */
	public function write($filename, $format=null){
		if(!empty($format)){
			$this->image->setImageFormat($format);
		}
		if(!is_dir(dirname($filename))){
			mkdir(dirname($filename), 0777, true);
		}
		$this->image->writeImage($filename);
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
		$this->image->setImageFormat($format);
		print($this->image);
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
		$this->image->setImageFormat($format);
		return $this->image->getImageBlob();
	}
	
	
	/**
	 * 画像の一部を抽出する
	 * @param integer $width 抽出する幅
	 * @param integer $height 抽出する高さ
	 * @param integer $x 抽出する領域の左上の X 座標
	 * @param integer $y 抽出する領域の左上の Y 座標
	 * @return \tt\image\Imagick
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
		$this->image->cropImage($width,$height,$x,$y);
		
		return $this;
	}
	
	/**
	 * 画像のサイズを変更する
	 * @param integer $width 変更後の幅
	 * @param integer $height 変更後の高さ
	 * @return \tt\image\Imagick
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
		
		$this->image->scaleImage($cw,$ch);
		
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
	 * @return \tt\image\Imagick
	 */
	public function rotate($angle,$background_color='#000000'){
		$this->image->rotateImage($background_color,$angle);
		
		return $this;
	}
	
	/**
	 * マージ
	 * @param integer $x
	 * @param integer $y
	 * @param \tt\image\Imagick $imagick
	 * @param integer $composite imagick::COMPOSITE_*
	 * @return \tt\image\Imagick
	 * @see https://www.php.net/manual/ja/imagick.constants.php
	 */
	public function merge($x,$y,\tt\image\Imagick $imagick,$composite=\Imagick::COMPOSITE_OVER){
		$this->image->compositeImage(
			$imagick->image,
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
		$w = $this->image->getImageWidth();
		$h = $this->image->getImageHeight();
		
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
	 * @return \tt\image\Imagick
	 * @see https://www.php.net/manual/ja/imagick.setoption.php
	 */
	public function set_option($k,$v){
		$this->image->setOption($k,$v);
		return $this;
	}
	
	/**
	 * 差分の抽出
	 * @param \tt\image\Imagick $imagick
	 * @return \tt\image\Imagick
	 */
	public function diff(\tt\image\Imagick $imagick){
		$result = $this->image->compareImages($imagick->image, \Imagick::METRIC_MEANSQUAREERROR);
		
		$diff = new static(__FILE__);
		$diff->image = $result[0];
		
		return $diff;
	}
	
	/**
	 * 点を描画する
	 * @param array $xys [[x,y]]の２次元配列
	 * @param string $color
	 */
	public function point(array $xys,$color){
		$draw = new \ImagickDraw();
		$draw->setFillColor(new \ImagickPixel($color));
		
		foreach($xys as $xy){
			$draw->point($xy[0],$xy[1]);
		}
		$this->image->drawImage($draw);
		
		return $this;
	}
	
	/**
	 * 矩形を描画する
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param integer $height
	 * @param string $color
	 * @param integer $thickness 線の太さ (塗り潰し時無効)
	 * @param boolean $fill 塗りつぶす
	 * @param integer $alpha 0〜127 (透明) PNGでのみ有効
	 * @return \tt\image\Imagick
	 */
	public function rectangle($x,$y,$width,$height,$color,$thickness=1,$fill=false,$alpha=0){
		$draw = $this->get_draw($color,$thickness,$fill,$alpha);
		$draw->rectangle($x,$y,$x + $width,$y + $height);
		$this->image->drawImage($draw);
		
		return $this;
	}
	/**
	 * 線を描画
	 * @param integer $sx 始点x
	 * @param integer $sy 始点y
	 * @param integer $ex 終点x
	 * @param integer $ey 終点y
	 * @param string $color
	 * @param number $thickness 線の太さ (塗り潰し時無効)
	 * @param number $alpha 0〜127 (透明) PNGでのみ有効
	 * @return \tt\image\Imagick
	 */
	public function line($sx,$sy,$ex,$ey,$color,$thickness=1,$alpha=0){
		$draw = $this->get_draw($color,$thickness,false,$alpha);
		$draw->line($sx,$sy,$ex,$ey);
		$this->image->drawImage($draw);
		
		return $this;
	}
	
	/**
	 * 楕円を描画する
	 * @param integer $cx 中心点x
	 * @param integer $cy 中心点y
	 * @param integer $width 幅直径
	 * @param integer $height 高さ直径
	 * @param string $color 
	 * @param number $thickness 線の太さ (塗り潰し時無効)
	 * @param boolean $fill 塗りつぶす
	 * @param number $alpha 0〜127 (透明) PNGでのみ有効
	 * @return \tt\image\Imagick
	 */
	public function ellipse($cx,$cy,$width,$height,$color,$thickness=1,$fill=false,$alpha=0){
		$draw = $this->get_draw($color,$thickness/2,$fill,$alpha);
		$draw->ellipse($cx,$cy,$width/2,$height/2,0,360);
		$this->image->drawImage($draw);
		
		return $this;
	}
	
	private function get_draw($color,$thickness=1,$fill=false,$alpha=0){
		$draw = new \ImagickDraw();
		
		if($fill){
			$draw->setFillColor(new \ImagickPixel($color));
			
			if($alpha > 0){
				$draw->setFillOpacity(round($alpha/127,3));
			}
		}else{
			$draw->setFillOpacity(0);
			
			if($thickness > 0){
				$draw->setStrokeColor(new \ImagickPixel($color));
				$draw->setStrokeWidth($thickness);
				
				if($alpha > 0){
					$draw->setStrokeOpacity(round($alpha/127,3));
				}
			}
		}
		return $draw;
	}
	
	/**
	 * フォントファイルパスに名前を設定する
	 * @param string $font_path ttfファイルパス
	 * @param string $font_name フォント名
	 */
	public static function set_font($font_path,$font_name=null){
		if(empty($font_name)){
			$font_name = preg_replace('/^(.+)\..+$/','\\1',basename($font_path));
		}
		if(!is_file($font_path)){
			throw new \tt\image\exception\AccessDeniedException($font_name.' access denied');
		}
		self::$font_path[$font_name] = $font_path;
	}
	
	/**
	 * テキストを画像に書き込む
	 * @param integer $x 左上座標
	 * @param integer $y　左上座標
	 * @param string $font_color #FFFFFF
	 * @param float $font_point_size フォントサイズ
	 * @param string $font_name set_fontで指定したフォント名
	 * @param string $text テキスト
	 * @return \tt\image\Imagick
	 */
	public function text($x,$y,$font_color,$font_point_size,$font_name,$text){
		$font_point_size = ceil($font_point_size);
		
		$draw = $this->get_text_draw($font_point_size, $font_name);
		$draw->setStrokeColor(new \ImagickPixel($font_color));
		$draw->setFillColor(new \ImagickPixel($font_color));
		$draw->annotation($x,$y + $font_point_size,$text);
		
		$this->image->drawImage($draw);
		return $this;
	}
	
	/**
	 * テキストの幅と高さ
	 * @param number $font_point_size フォントサイズ
	 * @param string $font_name フォント名
	 * @param string $text テキスト
	 * @return number[] [width,height]
	 */
	public function get_text_size($font_point_size,$font_name,$text){
		$draw = $this->get_text_draw($font_point_size, $font_name);
		$metrics = $this->image->queryFontMetrics($draw,$text);
		$w = $metrics['textWidth'];
		$h = $metrics['textHeight'];
		
		return [$w,$h];
	}
	
	private function get_text_draw($font_point_size,$font_name){
		if(!isset(self::$font_path[$font_name])){
			throw new \tt\image\exception\UndefinedException('undefined font `'.$font_name.'`');
		}
		$font_point_size = ceil($font_point_size * 1.3);
		
		$draw = new \ImagickDraw();
		$draw->setFont(self::$font_path[$font_name]);
		$draw->setFontSize($font_point_size);
		
		return $draw;
	}

	/**
	 * 画像の情報
	 *  integer width
	 *  integer height
	 *  integer orientation 画像の向き 1: PORTRAIT, 2: LANDSCAPE, 3: SQUARE
	 *  string mime 画像形式のMIMEタイプ
	 *  integer bits
	 *  integer channels 1: GRAY, 3: RGB, 4: CMYK
	 *  boolean broken 画像ファイルとして破損しているか
	 *  
	 * @param string $filename
	 * @return mixed{}
	 * @see http://jp2.php.net/manual/ja/function.getimagesize.php
	 * @see http://jp2.php.net/manual/ja/function.image-type-to-mime-type.php
	 */
	public static function get_info($filename){
		if(!is_file($filename)){
			throw new \tt\image\exception\AccessDeniedException($filename.' not found');
		}
		$info = getimagesize($filename);
		$mime = $info['mime'] ?? null;
		$broken = null;
		
		if($mime == 'image/jpeg'){
			$broken = (['ffd8','ffd9'] != self::check_file_type($filename, 2, 2));
		}else if($mime == 'image/png'){
			$broken = (['89504e470d0a1a0a','0000000049454e44ae426082'] != self::check_file_type($filename, 8, 12));
		}else if($mime == 'image/gif'){
			$broken = (['474946','3b'] != self::check_file_type($filename, 3, 1));
		}
		
		return [
			'width'=>$info[0],
			'height'=>$info[1],
			'orientation'=>self::judge_orientation($info[0],$info[1]),
			'mime'=>$mime,
			'bits'=>$info['bits'] ?? null,
			'channels'=>$info['channels'] ?? null,
			'broken'=>$broken,
		];
	}

	private static function check_file_type($filename,$header,$footer){
		$fp = fopen($filename,'rb');
		$a = unpack('H*',fread($fp,$header));
		fseek($fp,$footer * -1,SEEK_END);
		$b = unpack('H*',fread($fp,$footer));
		fclose($fp);
		return [($a[1] ?? null),($b[1] ?? null)];
	}
	
	private static function judge_orientation($w,$h){
		if($w > 0 && $h > 0){
			$d = $h / $w;
			
			if($d <= 1.02 && $d >= 0.98){
				return self::ORIENTATION_SQUARE;
			}else if($d > 1){
				return self::ORIENTATION_PORTRAIT;
			}
			return self::ORIENTATION_LANDSCAPE;
		}
		return null;
	}

}