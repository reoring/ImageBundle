<?php

namespace Reoring\ImageBundle\Service;

class ExifOrientation
{
  /**
   * 指定ファイルの左上がメモリの先頭となるように補正して画像を返す
   *
   * @static
   * @param string $path
   * @return \Imagick
   * @throws \Exception
   */
  public static function rotate($filePath)
  {
    $orientation = self::getOrientationFromFile($filePath);

    $image = new \Imagick($filePath);

    return self::rotateImage($image, $orientation);
  }

  /**
   * @static
   * @param \Imagick $image
   * @param int $orientation
   * @return \Imagick
   */
  private static function rotateImage(&$image, $orientation)
  {
    switch($orientation) {
      case 0: // 未定義
        break;
      case 1: // 通常
        break;
      case 2: // 左右反転
        $image->flopImage();
        break;
      case 3: // 180°回転
        $image->rotateImage(new \ImagickPixel(), 180);
        break;
      case 4: // 上下反転
        $image->flipImage();
        break;
      case 5: // 反時計回りに90°回転 上下反転
        $image->rotateImage(new \ImagickPixel(), 270);
        $image->flipImage();
        break;
      case 6: // 時計回りに90°回転
        $image->rotateImage(new \ImagickPixel(), 90);
        break;
      case 7: // 時計回りに90°回転 上下反転
        $image->rotateImage(new \ImagickPixel(), 90);
        $image->flipImage();
        break;
      case 8: // 反時計回りに90°回転
        $image->rotateImage(new \ImagickPixel(), 270);
        break;
    }

    return $image;
  }

  /**
   * @static
   * @param string $path
   * @return int
   * @throws \Exception
   */
  private static function getOrientationFromFile($path)
  {
    $exif = new Exif();
    $exif->read($path);

    if (!$exif->isExist()) {
      throw new \Exception('Exif not found in file: path=' . $path);
    }

    if (!$exif->has('Orientation')) {
      throw new \Exception('Orientation not found in Exif: filePath=' . $path);
    }

    return (int)$exif->Orientation;
  }
}