<?php

namespace Reoring\ImageBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Reoring\ImageBundle\Service\Image;
use Reoring\ImageBundle\Service\imposition\ImpositionPage;
use Reoring\ImageBundle\Service\imposition\ImpositionAlignmentLogic;

class Imposition
{
  /**
   * 面付け処理実行前に、下地画像のパスを格納しておく
   *
   * @var string
   */
  private $basePaperPath = '';

  /**
   * 面付け処理実行前に、出力先ディレクトリのパスを格納しておく
   *
   * @var string
   */
  private $outputDirPath = '';

  /**
   * 面付け処理実行前に、ページ画像パスのリストを格納しておく
   *
   * @var array of string
   */
  private $pagePathes = array();

  /**
   * バリデーションが通らなかったときに、エラーメッセージが格納される
   *
   * @var string
   */
  private $errorMessage = '';

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
   * @var ContainerInterface
   */
  private $container;

  /**
   * @param string $path
   */
  public function setBasePaperPath($path)
  {
    $this->basePaperPath = $path;
  }

  /**
   * @param string $path
   */
  public function setOutputDirPath($path)
  {
    $this->outputDirPath = $path;
  }

  /**
   * @param array of string $pathes
   */
  public function setPages($pathes)
  {
    $this->pagePathes = $pathes;
  }

  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
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
   * 面付け処理が実行可能かどうか
   * 実行不可能な場合は、errorMessage が格納される
   *
   * @return bool  true: 実行可能、false: 実行不可能
   */
  public function isValid()
  {
    if (!Image::isImageFile($this->basePaperPath)) {
      $this->errorMessage = '下地画像が読み込めません:' . $this->basePaperPath;
      return false;
    }

    if (count($this->pagePathes) === 0) {
      $this->errorMessage = 'ページ画像が設定されていません';
      return false;
    }

    foreach ($this->pagePathes as $pagePath) {
      if (!Image::isImageFile($pagePath)) {
        $this->errorMessage = 'ページ画像が読み込めません:' . $pagePath;
        return false;
      }
    }

    if (!$this->mkdir($this->outputDirPath)){
      $this->errorMessage = '出力先ディレクトリを作成できません:' . $this->outputDirPath;
      return false;
    }

    return true;
  }

  /**
   * 面付け処理を実行する
   */
  public function run()
  {
    $pages = array();

    $pageNumber = 1;

    // ページの配列を作成
    foreach ($this->pagePathes as $pagePath) {
      $image = new \Imagick($pagePath);
      $image->setImageResolution($this->xResolution, $this->yResolution);

      $page = new ImpositionPage();
      $page->setImage($image);
      $page->setNumber = $pageNumber;

      $pages[] = $page;

      ++$pageNumber;
    }

    // 面付けロジックを実行
    $logic = new ImpositionAlignmentLogic();
    $logic->setResolution($this->xResolution, $this->yResolution);

    $logic->setContainer($this->getContainer());
    $logic->setPages($pages);
    $logic->run();
    $papers = $logic->getPapers();

    // 下地画像を作成
    $baseImage = new \Imagick($this->basePaperPath);
    $baseImage->setImageResolution($this->xResolution, $this->yResolution);

    // 下地画像を各出力用紙に設定して、座標計算
    foreach ($papers as $i => $paper) {
      $paper->setBaseImage($baseImage);
      $paper->calculatePagePositions();
      $paper->composite();

      $paperImage = $paper->getImage();

      $path = $this->getPaperImagePath($i);
      $paperImage->writeimage($path);
    }

    foreach ($papers as $i => $paper) {
      $paper->destroy();
    }
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

    return mkdir($this->outputDirPath, 0777, true);
  }

  /**
   * 出力用紙パスを生成する
   *
   * @param $paperIdx 出力用紙のインデックス
   * @return string ファイルパス
   */
  private function getPaperImagePath($paperIdx)
  {
    return $this->outputDirPath . '/' . sprintf('%04d', $paperIdx) . '.jpg';
  }
}
