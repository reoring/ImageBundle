<?php

namespace Reoring\ImageBundle\Service\imposition;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Reoring\ImageBundle\Service\imposition\ImpositionPage;
use Reoring\ImageBundle\Service\imposition\ImpositionPaper;

/**
 * 面付け配置クラス
 */
class ImpositionAlignmentLogic
{
  /**
   * 配置実行前にページの配列をセットしておく
   *
   * @var array of ImpositionPage
   */
  private $pages;

  /**
   * 配置実行後に出力用紙の配列がセットされる
   *
   * @var array of ImpositionPaper
   */
  private $papers;

  /**
   * @var ContainerInterface
   */
  private $container;

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
   * 配置実行前にページの配列をセットしておく
   *
   * @param $pages array of ImpositionPage
   */
  public function setPages($pages)
  {
    $this->pages = $pages;
  }

  /**
   * 配置実行後に出力用紙の配列がセットされる
   *
   * @return array of ImpositionPaper
   */
  public function getPapers()
  {
    return $this->papers;
  }

  /**
   * ページ数を取得
   *
   * @return int
   */
  public function getPageCount()
  {
    return count($this->pages);
  }

  /**
   * @param null|\Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  public function setContainer($container = null)
  {
    $this->container = $container;
  }

  /**
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   */
  private function getContainer()
  {
    return $this->container;
  }

  /**
   * 面付け配置を実行
   */
  public function run()
  {
    $pageCount = $this->getPageCount();
    // ページ数が端数の場合は4の倍数になるように切り上げる
    $roundedPageCount = ceil($this->getPageCount() * 0.25) * 4;
    $paperCount = $roundedPageCount * 0.5;

    $this->msg("--- 面付け処理ここから ---");

    $this->msg("この本は、全部で" . $pageCount . "ページです。");
    $this->msg("端数を丸めると、" . $roundedPageCount . "ページになります。");
    $this->msg("全部で" . $paperCount . "枚の用紙を作成します。");

    // 出力用紙を作成しておく
    $this->papers = array();
    for ($paperNumber = 1; $paperNumber <= $paperCount; ++$paperNumber) {
      $paper = new ImpositionPaper();
      $paper->setResolution($this->xResolution, $this->yResolution);

      $paper->setNumber($paperNumber);
      $this->papers[] = $paper;
      $this->msg($paperNumber . "枚目の用紙を作成しました。");
    }

    $pageNumber = 1;

    $odd = true;

    foreach ($this->papers as $paperI => $paper) {
      $paperNumber = $paperI + 1;

      if ($pageNumber <= $pageCount) {
        $page = $this->pages[$pageNumber - 1];

        if ($odd) {
          $paper->setRightPage($page);
          $this->msg($paperNumber . "枚目の用紙の右側に". $pageNumber . "ページをセットしました。");
        } else {
          $paper->setLeftPage($page);
          $this->msg($paperNumber . "枚目の用紙の左側に". $pageNumber . "ページをセットしました。");
        }
      }

      $lPageNumber = $roundedPageCount - $pageNumber + 1;
      if ($lPageNumber <= $pageCount) {
        $page = $this->pages[$lPageNumber - 1];

        if ($odd) {
          $paper->setLeftPage($page);
          $this->msg($paperNumber . "枚目の用紙の左側に". $lPageNumber . "ページをセットしました。");
        } else {
          $paper->setRightPage($page);
          $this->msg($paperNumber . "枚目の用紙の右側に". $lPageNumber . "ページをセットしました。");
        }
      }

      ++$pageNumber;
      $odd = !$odd;
    }

    $this->msg("--- 面付け処理ここまで ---");
  }

  function msg($str) {
    echo $str . "\n";

    $container = $this->getContainer();

    if ($container === null) {
      return;
    }

    $logger = $container->get("logger");
    $logger->info($str);
  }
}