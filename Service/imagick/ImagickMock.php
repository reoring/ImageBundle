<?php

namespace Reoring\ImageBundle\Service\imagick;

/**
 * Imagickと同等のインターフェイスを持つ、UnitTest用のモッククラス
 * 足りないメソッド等は逐次追加する
 */
class ImagickMock
{
  /**
   * @var int
   */
  private $imageWidth = 100;

  /**
   * @var int
   */
  private $imageHeight = 100;

  /**
   * @var string
   */
  private $filePath = '';

  /**
   * @param string $filePath
   */
  public function __construct($filePath = '')
  {
    $this->filePath = $filePath;
  }

  /**
   * @param int $imageWidth
   */
  public function setImageWidth($imageWidth)
  {
    $this->imageWidth = $imageWidth;
  }

  /**
   * @return int
   */
  public function getImageWidth()
  {
    return $this->imageWidth;
  }

  /**
   * @param int $imageHeight
   */
  public function setImageHeight($imageHeight)
  {
    $this->imageHeight = $imageHeight;
  }

  /**
   * @return int
   */
  public function getImageHeight()
  {
    return $this->imageHeight;
  }
}
