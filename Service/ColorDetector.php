<?php

namespace Reoring\ImageBundle\Service;

/**
 * 画像中のある位置に含まれる色の割合を判断する
 *
 */
class ColorDetector
{
  private $path;
  private $image;
  
  private $detectPixelCount = 0;
  private $totalPixelCount = 0;

  /**
   * @param string $path 画像へのパス
   * @throws \Exception
   */
  public function __construct($path)
  {
    $this->path = $path;

    if (!is_readable($path)) {
      throw new \Exception($path . " not found");
    }

    $this->image = new \Imagick($path);
  }
  
  /**
   * 検出を実行
   *
   * @param int x
   * @param int y
   * @param int width
   * @param int height
   * @param int colorThreshold optional 色の閾値 デフォルトは50以下
   * @param int threshold optional その色が含まれる割合 デフォルトは50%
   *
   * @return boolean 検出結果
   */
  public function detect($x, $y, $width, $height, $colorThreshold = 30, $threshold = 30)
  {
    $this->detectPixelCount = 0;
    $this->totalPixelCount = $width * $height;
    
    $pite = $this->image->getPixelIterator();
    $pite->newPixelRegionIterator($this->image, $x, $y, $width, $height);
    
    foreach ($pite as $row) {
      foreach ($row as $pixel) {
        $color = $pixel->getColor();
        
        if ($color['r'] < $colorThreshold &&
            $color['g'] < $colorThreshold &&
            $color['b'] < $colorThreshold)
        {
          $this->detectPixelCount++;
        }
        
        $percentage = ($this->detectPixelCount / $this->totalPixelCount) * 100;
        
        if ($percentage > $threshold) {
          $this->reset();
          return true;
        }
      }
    }
    
    $this->reset();
    return false;
  }

  /**
   * 2つの画像が一致しているか比較する
   *
   * @param $path 比較対象の画像パス
   * @param int $colorThreshold 色のしきい値
   * @param int $threshold しきい値以内に収まっている割合(%)
   * @return bool 結果
   * @throws \Exception
   */
  public function compare($path, $colorThreshold = 30, $threshold = 30)
  {
    if (!is_readable($path)) {
      throw new \Exception($path . " not found");
    }

    $image1 = $this->image;
    $image2 = new \Imagick($path);

    $width  = $image1->getImageWidth();
    $height = $image1->getImageHeight();

    if ($width !== $image2->getImageWidth() || $height !== $image2->getImageHeight()) {
      throw new \Exception('image size miss match: path1=' . $this->path . ', size=('. $width .', ' . $height . ')' . ', path2=' . $path . ', size=(' . $image2->getImageWidth() . ', ' . $image2->getImageHeight() . ')');
    }

    $this->totalPixelCount = $width * $height;

    $pi1 = $image1->getPixelIterator();
    $pi2 = $image2->getPixelIterator();

    for (; $pi1->valid() && $pi2->valid(); $pi1->next(), $pi2->next()) {
      $row1 = $pi1->current();
      $row2 = $pi2->current();

      for ($i = 0, $length = count($row1); $i < $length; ++$i) {
        $color1 = $row1[$i]->getColor();
        $color2 = $row2[$i]->getColor();

        abs($color1['r'] - $color2['r']);
        if (abs($color1['r'] - $color2['r']) < $colorThreshold &&
            abs($color1['g'] - $color2['g']) < $colorThreshold &&
            abs($color1['b'] - $color2['b']) < $colorThreshold)
        {
          $this->detectPixelCount++;
        }

        $percentage = ($this->detectPixelCount / $this->totalPixelCount) * 100;

        if ($percentage > $threshold) {
          $this->reset();
          return true;
        }
      }
    }

    $this->reset();
    return false;
  }

  private function reset()
  {
    $this->detectPixelCount = 0;
  }
}
