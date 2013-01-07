<?php

namespace Reoring\ImageBundle\Service;

/**
 * 画像リサイザ
 * 
 * @author morireo
 */
class Resizer
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
   * 余白部分の背景色
   * 
   * @var string white | black | red | green | ...
   */
  const BACKGROUND_COLOR = "white";

  /**
   * 指定ファイルの画像をリサイズする
   *
   * @param string $filePath
   * @param int $toWidth
   * @param int $toHeight
   * @param int $policy
   * @param int|null $resolution
   * @return \Imagick
   * @throws \Exception
   */
  public function resize($filePath, $toWidth, $toHeight, $policy = self::POLICY_CIRCUMSCRIBE, $resolution = null, $rotation = false)
  {
    if (!is_readable($filePath)) {
      throw new \Exception("file not readable $filePath");
    }

    $image = new \Imagick($filePath);

    return $this->resizeImage($image, $toWidth, $toHeight, $policy, $resolution);
  }

    public function createImage($content)
    {
        $im = new \Imagick();
        $im->readimageblob($content);

        return $im;
    }

  /**
   * 画像をリサイズする
   *
   * @param \Imagick $image
   * @param int $toWidth
   * @param int $toHeight
   * @param int $policy
   * @param int|null $resolution
   * @return \Imagick
   * @throws \Exception
   */
  public function resizeImage(&$image, $toWidth, $toHeight, $policy = self::POLICY_CIRCUMSCRIBE, $resolution = null, $rotation = false)
  {
    if ($toHeight === 0) {
      $toHeight = 150;
    }

    if ($toWidth === 0) {
      $toWidth = 150;
    }

    if ($resolution !== null) {
      $image->setCompressionQuality($resolution);
    }

    $image->setImageResolution(300, 300);

    $canvas = new \Imagick();
    $canvas->newImage($toWidth, $toHeight, new \ImagickPixel(self::BACKGROUND_COLOR));
    $canvas->setcompressionquality($resolution);
    $canvas->setImageResolution(300, 300);

    if ($rotation) {
      if (($image->getImageHeight() > $image->getImageWidth()) && ($canvas->getImageHeight() < $canvas->getImageWidth())) {
        $image->rotateimage('none', 270);
      } elseif (($image->getImageHeight() < $image->getImageWidth()) && ($canvas->getImageHeight() > $canvas->getImageWidth())) {
        $image->rotateimage('none', 270);
      }
    }
    
    $x = 0;
    $y = 0;

    if ($policy === self::POLICY_INSCRIBE) {
      $image->thumbnailImage($toWidth, $toHeight, true);
    
      if ($image->getImageHeight() === $toHeight) {
        // 高さが一致
        $x = ($toWidth - $image->getImageWidth()) / 2;
      } else {
        // 幅が一致
        $y = ($toHeight - $image->getImageHeight()) / 2; 
      }
    } elseif ($policy === self::POLICY_CIRCUMSCRIBE) {
      if (($image->getImageHeight() / $toHeight) > $image->getImageWidth() / $toWidth) {
        $image->thumbnailImage($toWidth, 0, false);
        $y = (($image->getImageHeight() - $toHeight) / 2) * -1;
      } else {
        $image->thumbnailImage(0, $toHeight, false);
        $x = (($image->getImageWidth() - $toWidth) / 2) * -1;
      }
    } else {
      throw new \Exception("policy not found");
    }
    
    // 画像合成
    $canvas->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, $x, $y);
    
    return $canvas;
  }
}
