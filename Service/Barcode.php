<?php

namespace Reoring\ImageBundle\Service;

use Reoring\ImageBundle\Service\Image;

class Barcode {
  /**
   * 印字位置X
   *
   * @var int
   */
  private $x;

  /**
   * 印字位置Y
   *
   * @var int
   */
  private $y;

  /**
   * 印字幅
   *
   * @var int
   */
  private $width;

  /**
   * 印字高
   *
   * @var int
   */
  private $height;

  /**
   * バーコードとして印字する文字列
   *
   * @var string
   */
  private $code;

  /**
   * バーコード印字対象の画像ファイルパス
   *
   * @var string
   */
  private $sourceFilePath;

  /**
   * 生成されるバーコードイメージ
   *
   * @var \Imagick
   */
  private $barcodeImage;

  /**
   * 出力先ファイルパス
   *
   * @var string
   */
  private $outputFilePath;

  /**
   * バーコードフォントのパス
   *
   * @var string
   */
  private $fontFilePath;

  /**
   * isValidを実行後、処理不可能と判定された場合、エラーメッセージが格納される
   *
   * @var string
   */
  private $errorMessage;

  /**
   * @var float
   */
  private $xResolution = 300.0;

  /**
   * @var float
   */
  private $yResolution = 300.0;

  /**
   * 画像の解像度を設定する(初期値は 300.0)
   *
   * @param $xResolution
   * @param $yResolution
   */
  public function setResolution($xResolution, $yResolution)
  {
    $this->xResolution = $xResolution;
    $this->yResolution = $yResolution;
  }

  public function __construct()
  {
    $this->fontFilePath = __DIR__ . '/../Resources/fonts/NW7.ttf';
  }

  /**
   * バーコードを印字する範囲を指定する
   *
   * @param int $x
   * @param int $y
   * @param int $width
   * @param int $height
   */
  public function setPosition($x, $y, $width, $height)
  {
    $this->x      = (int)$x;
    $this->y      = (int)$y;
    $this->width  = (int)$width;
    $this->height = (int)$height;
  }

  /**
   * バーコードとして印字する文字列
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = (string)$code;
  }

  /**
   * バーコード印字対象の画像ファイルパス
   *
   * @param string $path
   */
  public function setSourceFilePath($path)
  {
    $this->sourceFilePath = (string)$path;
  }

  /**
   * 出力先ファイルパス
   *
   * @param string $path
   */
  public function setOutputFilePath($path)
  {
    $this->outputFilePath = (string)$path;
  }

  /**
   * 処理が実行可能かどうか
   *
   * @return bool
   */
  public function isValid()
  {
    if ($this->x === null || $this->y === null || $this->width === null || $this->height === null) {
      $this->errorMessage = "バーコード印字位置が正しく設定されていません:($this->x,$this->y,$this->width,$this->height)";
      return false;
    }

    if ($this->width <= 0 || $this->height <= 0) {
      $this->errorMessage = "バーコード印字範囲が正しく設定されていません:(width:$this->width,height:$this->height)";
      return false;
    }

    if ($this->code === null) {
      $this->errorMessage = "バーコード文字列が設定されていません";
      return false;
    }

    if ($this->sourceFilePath === null) {
      $this->errorMessage = "印字対象の画像ファイルパスが設定されていません";
      return false;
    }

    if (!Image::isImageFile($this->sourceFilePath)) {
      $this->errorMessage = "印字対象の画像ファイルが読み込めません:$this->sourceFilePath";
    }

    if ($this->outputFilePath === null) {
      $this->errorMessage = "出力先ファイルパスが設定されていません";
      return false;
    }

    if (is_dir($this->outputFilePath)) {
      $this->errorMessage = "出力先ファイルパスにディレクトリが存在します:$this->outputFilePath";
      return false;
    }

    if (!$this->mkdir(dirname($this->outputFilePath))) {
      $this->errorMessage = '出力先ディレクトリを作成できません:' . $this->outputFilePath;
      return false;
    }

    return true;
  }

  /**
   * 印字処理を実行する
   */
  public function printBarcode()
  {
    $this->createBarcodeImage();
    $x = $this->x + ($this->width  - $this->barcodeImage->getImageWidth())  * 0.5;
    $y = $this->y + ($this->height - $this->barcodeImage->getImageHeight()) * 0.5;

    $sourceImage = new \Imagick($this->sourceFilePath);
    $sourceImage->setImageResolution($this->xResolution, $this->yResolution);

    $canvas = new \Imagick();
    $canvas->newimage($sourceImage->getImageWidth(), $sourceImage->getImageHeight(), 'none');
    $canvas->setImageResolution($this->xResolution, $this->yResolution);

    $canvas->compositeImage($sourceImage, \Imagick::COMPOSITE_DEFAULT, 0, 0);
    $canvas->compositeImage($this->barcodeImage, \Imagick::COMPOSITE_DEFAULT, $x, $y);
//    $canvas->setImageFormat("jpg");
    $canvas->writeimage($this->outputFilePath);
    $canvas->destroy();

    $sourceImage->destroy();
    $this->barcodeImage->destroy();
  }

  /**
   * バーコード画像を生成する
   */
  private function createBarcodeImage()
  {
    $fontSize = 80;
    $this->barcodeImage = new \Imagick();

    $rv = $this->measureBarcodeImageSize($fontSize);

    $scaleX = $this->width  / $rv->width;
    $scaleY = $this->height / $rv->height;
    $scale = $scaleX < $scaleY ? $scaleX : $scaleY;

    $fontSize = (int)($fontSize * $scale);

    $rv = $this->measureBarcodeImageSize($fontSize);

    while ($rv->width < $this->width  && $rv->height < $this->height) {
      $rv = $this->measureBarcodeImageSize(++$fontSize);
//      echo '+';
    }

    while ($this->width < $rv->width || $this->height < $rv->height) {
      $rv = $this->measureBarcodeImageSize(--$fontSize);
//      echo '-';
    }

    $this->barcodeImage->newImage($rv->width, $rv->height, 'transparent');
    $this->barcodeImage->setImageResolution($this->xResolution, $this->yResolution);

    $this->barcodeImage->drawImage($rv->draw);
  }

  /**
   * 引数指定のフォントサイズでバーコード描画した場合の幅と高さを計測する
   *
   * @param $fontSize
   * @return \stdClass
   */
  private function measureBarcodeImageSize($fontSize)
  {
    $idraw = new \ImagickDraw();
    $idraw->setFont($this->fontFilePath);
    $idraw->setGravity(\Imagick::GRAVITY_CENTER);
    $idraw->setFontSize($fontSize);
    $idraw->setFillColor('black');

    $idraw->annotation(0, 0, $this->code);

    $metrics = $this->barcodeImage->queryFontMetrics($idraw, $this->code);

    $rv = new \stdClass();
    $rv->width  = $metrics['textWidth'] * 1.1;
    $rv->height = $metrics['textHeight'] * 1.1;
    $rv->draw   = $idraw;

    return $rv;
  }

  /**
   * エラーメッセージを取得する
   *
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }

  /**
   * 書き込み可能(ファイルモード0777)なディレクトリを作成する。
   * 親ディレクトリが存在しない場合は再帰的に作成する。
   *
   * @param $path
   * @return bool true: 成功、false: 失敗
   */
  private function mkdir($path)
  {
    if (is_dir($path)) {
      return chmod($path, 0777);
    }

    return mkdir($path, 0777, true);
  }
}