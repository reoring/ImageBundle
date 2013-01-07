<?php

namespace Reoring\ImageBundle\Service;

class Image
{
  /**
   * 内接リサイズ
   *
   * 印刷画像サイズに元画像全体が収まる。
   * アスペクト比が異なる場合は余白が生じる。
   *
   * @var int
   */
  const POLICY_INSCRIBE = 0;

  /**
   * 外接リサイズ
   *
   * 印刷画像サイズのアスペクト比に元画像を合わせる。
   * 元画像が対象アスペクト比と異なるとはみだし部分は削除される。
   *
   * @var int
   */
  const POLICY_CIRCUMSCRIBE = 1;

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

  /**
   * サムネイルを作成する
   *
   * @param string $srcPath 元画像
   * @param string $dstPath 保存先
   * @param int $width   幅
   * @param int $height  高さ
   * @param int $policy
   */
  public function thumbnail($srcPath, $dstPath, $width, $height, $policy = self::POLICY_INSCRIBE)
  {
    $image = new \Imagick($srcPath);
    $image->setImageResolution($this->xResolution, $this->yResolution);

    if ($policy === self::POLICY_INSCRIBE) {
      $image->thumbnailImage($width, $height, true);
    } else {
      $toWidth = $width;
      $toHeight = $height;

      if ($image->getImageWidth() * $height > $width * $image->getImageHeight()) {
        $toWidth = $height / $image->getImageHeight() * $image->getImageWidth();
      } else {
        $toHeight = $width / $image->getImageWidth() * $image->getImageHeight();
      }

      $image->thumbnailImage($toWidth, $toHeight);
    }
    $image->writeimage($dstPath);

    $image->destroy();
  }

  /**
   * 2つの画像を合成する
   *
   * @param string $srcPath1 下側の元画像
   * @param string $srcPath2 上側の元画像
   * @param string $dstPath  保存先
   * @param float $opacity   不透明度
   */
  public function overlay($srcPath1, $srcPath2, $dstPath, $opacity = 1.0)
  {
    $image1 = new \Imagick($srcPath1);
    $image1->setImageResolution($this->xResolution, $this->yResolution);

    $image2 = new \Imagick($srcPath2);
    $image2->setImageResolution($this->xResolution, $this->yResolution);

    if ($opacity < 1.0) {
      $image2->setImageOpacity($opacity);
    }

    $image1CenterX = $image1->getImageWidth() * 0.5;
    $image1CenterY = $image1->getImageHeight() * 0.5;

    $image2CenterX = $image2->getImageWidth() * 0.5;
    $image2CenterY = $image2->getImageHeight() * 0.5;

    $canvas = new \Imagick();
    $canvas->newimage($image1->getImageWidth(), $image1->getImageHeight(), 'none');
    $canvas->setImageResolution($this->xResolution, $this->yResolution);

    $canvas->compositeImage($image1, \Imagick::COMPOSITE_DEFAULT, 0, 0);
    $canvas->compositeImage($image2, \Imagick::COMPOSITE_DEFAULT, $image1CenterX - $image2CenterX, $image1CenterY - $image2CenterY);
    $canvas->setImageFormat("jpg");
    $canvas->writeimage($dstPath);

    $image1->destroy();
    $image2->destroy();
    $canvas->destroy();
  }

  /**
   * 指定の画像ファイルが、Imagick で読み込み可能かどうか
   *
   * @param string $path 画像ファイルパス
   * @return bool true: 読み込み可能、false: 読み込み不可能
   */
  public static function isImageFile($path)
  {
    if (is_dir($path) || !is_readable($path)) {
      return false;
    }

    try {
      new \Imagick($path);
    } catch (\Exception $e) {
      return false;
    }

    return true;
  }
}