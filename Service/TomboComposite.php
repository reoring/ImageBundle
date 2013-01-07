<?php

namespace Reoring\ImageBundle\Service;

use Reoring\ImageBundle\Service\Image;

class TomboComposite {
  /**
   * @var string
   */
  private $basePaperPath = '';

  /**
   * @var string
   */
  private $sourceFilePath = '';

  /**
   * @var string
   */
  private $outputFilePath = '';

  /**
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
   * 下地画像パス
   *
   * @param string $path
   */
  public function setBasePaperPath($path)
  {
    $this->basePaperPath = $path;
  }

  /**
   * トンボ合成対象の画像ファイルパス
   *
   * @param string $path
   */
  public function setSourceFilePath($path)
  {
    $this->sourceFilePath = $path;
  }

  /**
   * 出力先ファイルパス
   *
   * @param string $path
   */
  public function setOutputFilePath($path)
  {
    $this->outputFilePath = $path;
  }

  /**
   * 処理が実行可能かどうかを調べる
   *
   * @return bool
   */
  public function isValid()
  {
    if ($this->basePaperPath === '') {
      $this->errorMessage = '下地画像パスが設定されていません。';
      return false;
    }

    if (!Image::isImageFile($this->basePaperPath)) {
      $this->errorMessage = "下地画像ファイルが読み込めません:$this->basePaperPath";
    }

    if ($this->sourceFilePath === '') {
      $this->errorMessage = 'トンボ合成対象の画像ファイルパスが設定されていません。';
      return false;
    }

    if (!Image::isImageFile($this->sourceFilePath)) {
      $this->errorMessage = "トンボ合成対象の画像ファイルが読み込めません:$this->sourceFilePath";
    }

    if ($this->outputFilePath === '') {
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
   * 合成処理を実行する
   */
  public function printTombo()
  {
    $image = new Image();
    $image->setResolution($this->xResolution, $this->yResolution);
    $image->overlay($this->basePaperPath, $this->sourceFilePath, $this->outputFilePath);
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