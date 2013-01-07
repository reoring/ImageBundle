<?php

namespace Reoring\ImageBundle\Service\imposition;

use Reoring\ImageBundle\Service\imposition\ImpositionPage;

/**
 * 面付け出力用紙クラス
 */
class ImpositionPaper
{
  /**
   * 下地画像
   *
   * @var \Imagick
   */
  private $baseImage;

  /**
   * ページの配列
   *
   * @var array of ImpositionPage
   */
  private $pages;

  /**
   * 合成処理後、出力用紙画像が設定される
   *
   * @var \Imagick
   */
  private $paperImage;

  /**
   * 面付けロジックで生成された出力用紙番号が格納される
   *
   * @var int
   */
  private $paperNumber;

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
    $this->pages = array(null, null);
  }

  /**
   * 面付けロジックから設定する
   *
   * @param $number int
   */
  public function setNumber($number)
  {
    $this->paperNumber = $number;
  }

  /**
   * @return int
   */
  public function getNumber()
  {
    return $this->paperNumber;
  }

  /**
   * 下地画像を設定
   *
   * @para\Imagick $baseImage
   */
  public function setBaseImage($baseImage)
  {
    $this->baseImage = $baseImage;
  }

  /**
   * 各ページに座標計算させる
   */
  public function calculatePagePositions()
  {
    foreach ($this->pages as $page) {
      if ($page === null) {
        continue;
      }

      $page->calculatePosition();
    }
  }

  /**
   * 左ページを設定
   *
   * @param ImpositionPage $leftPage
   */
  public function setLeftPage($leftPage)
  {
    $leftPage->setPaper($this);
    $leftPage->setAlignmentToLeft();
    $this->pages[0] = $leftPage;
  }

  /**
   * 左ページを取得
   *
   * @return ImpositionPage
   */
  public function getLeftPage()
  {
    return $this->pages[0];
  }

  /**
   * 右ページを設定
   *
   * @param ImpositionPage $rightPage
   */
  public function setRightPage($rightPage)
  {
    $rightPage->setPaper($this);
    $rightPage->setAlignmentToRight();
    $this->pages[1] = $rightPage;
  }

  /**
   * 右ページを取得
   *
   * @return ImpositionPage
   */
  public function getRightPage()
  {
    return $this->pages[1];
  }

  /**
   * 下地画像設定後、ページの幅を取得
   *
   * @return int
   */
  public function getWidth()
  {
    return $this->baseImage->getImageWidth();
  }

  /**
   * 下地画像設定後、ページの高さを取得
   *
   * @return int
   */
  public function getHeight()
  {
    return $this->baseImage->getImageHeight();
  }

  /**
   * 座標計算、出力用紙番号設定後、下地画像にページ画像を合成する
   */
  public function composite()
  {
    $canvas = new \Imagick();
    $canvas->newimage($this->baseImage->getImageWidth(), $this->baseImage->getImageHeight(), 'none');
    $canvas->setImageResolution($this->xResolution, $this->yResolution);

    $canvas->compositeImage($this->baseImage, \Imagick::COMPOSITE_DEFAULT, 0, 0);

    $leftPage = $this->getLeftPage();
    if ($leftPage !== null) {
      $canvas->compositeImage($leftPage->getImage(), \Imagick::COMPOSITE_DEFAULT, $leftPage->getX(), $leftPage->getY());
    }

    $rightPage = $this->getRightPage();
    if ($rightPage !== null) {
      $canvas->compositeImage($rightPage->getImage(), \Imagick::COMPOSITE_DEFAULT, $rightPage->getX(), $rightPage->getY());
    }

    $this->paperImage = $canvas;
  }

  /**
   * 画像合成後、出力用紙を取得する
   *
   * @return \Imagick
   */
  public function getImage()
  {
    return $this->paperImage;
  }

  /**
   * Imagickメモリ開放
   */
  public function destroy()
  {
    $this->baseImage->destroy();
    $this->paperImage->destroy();

    foreach ($this->pages as $page) {
      if ($page === null) {
        continue;
      }

      $page->destroy();
    }
  }
}