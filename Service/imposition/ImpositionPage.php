<?php

namespace Reoring\ImageBundle\Service\imposition;

/**
 * 面付けページクラス
 */
class ImpositionPage
{
  const ALIGNMENT_LEFT = 'left';
  const ALIGNMENT_RIGHT = 'right';

  /**
   * ページ番号
   *
   * @var int
   */
  private $pageNumber;

  /**
   * @var string
   */
  private $imageFilePath;

  /**
   * @var string
   */
  private $alignment;

  /**
   * @var \Imagick
   */
  private $image;

  /**
   * @var ImpositionPaper
   */
  private $paper;

  private $x = 0;
  private $y = 0;

  /**
   * ページ画像を設定
   *
   * @param \Imagick $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }

  /**
   * ページ画像を取得
   *
   * @return \Imagick
   */
  public function getImage()
  {
    return $this->image;
  }

  /**
   * 出力用紙、ページ画像、配置を設定後、出力用紙内の座標を計算する
   */
  public function calculatePosition()
  {
    $hw = $this->paper->getWidth() * 0.5;

    if ($this->alignment === self::ALIGNMENT_LEFT) {
      $this->x = $hw - $this->getWidth();
    } else {
      $this->x = $hw;
    }

    $this->y = ($this->paper->getHeight() - $this->getHeight()) * 0.5;
  }

  /**
   * ページ画像を設定後、ページ幅を取得
   *
   * @return int
   */
  public function getWidth()
  {
    return $this->image->getImageWidth();
  }

  /**
   * ページ画像を設定後、ページ高を取得
   *
   * @return int
   */
  public function getHeight()
  {
    return $this->image->getImageHeight();
  }

  /**
   * 配置（左ページか右ページか）を設定
   *
   * @param $alignment
   */
  public function setAlignment($alignment)
  {
    $this->alignment = $alignment;
  }

  /**
   * このページを左ページとして設定
   */
  public function setAlignmentToLeft()
  {
    $this->alignment = self::ALIGNMENT_LEFT;
  }

  /**
   * このページを右ページとして設定
   */
  public function setAlignmentToRight()
  {
    $this->alignment = self::ALIGNMENT_RIGHT;
  }

  /**
   * 配置（左ページか右ページか）を取得
   *
   * @return string
   */
  public function getAlignment()
  {
    return $this->alignment;
  }

  /**
   * 出力用紙内の座標を計算後、X位置を取得
   *
   * @return int
   */
  public function getX()
  {
    return $this->x;
  }

  /**
   * 出力用紙内の座標を計算後、Y位置を取得
   *
   * @return int
   */
  public function getY()
  {
    return $this->y;
  }

  /**
   * 出力用紙を設定
   *
   * @param ImpositionPaper $paper
   */
  public function setPaper($paper)
  {
    $this->paper = $paper;
  }

  /**
   * 出力用紙を取得
   *
   * @return ImpositionPaper
   */
  public function getPaper()
  {
    return $this->paper;
  }

  /**
   * ページ番号を設定
   *
   * @param int $number
   */
  public function setNumber($number)
  {
    $this->pageNumber = $number;
  }

  /**
   * ページ番号を取得
   *
   * @return int
   */
  public function getNumber()
  {
    return $this->pageNumber;
  }

  /**
   * Imagickメモリ開放
   */
  public function destroy()
  {
    $this->image->destroy();
    $this->image = null;
    $this->paper = null;
  }
}