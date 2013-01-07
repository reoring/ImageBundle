<?php

namespace Reoring\ImageBundle\Tests\Service;

use PHPUnit_Framework_TestCase;

use Reoring\ImageBundle\Service\imposition\ImpositionAlignmentLogic,
    Reoring\ImageBundle\Service\imposition\ImpositionPaper,
    Reoring\ImageBundle\Service\imposition\ImpositionPage;

use Reoring\ImageBundle\Service\imagick\ImagickMock;

class ImpositionAlignmentLogicTest extends PHPUnit_Framework_TestCase
{
  /**
   * 1ページ配置ロジックのテスト
   */
  public function testAlignment01()
  {
    $papers = $this->createTestPapers(1);

    $paper = $papers[0];
//    echo var_export($paper, 1);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());
  }

  /**
   * 2ページ配置ロジックのテスト
   */
  public function testAlignment02()
  {
    $papers = $this->createTestPapers(2);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());

    $this->assertEquals(  2, $papers[1]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getY());
  }

  /**
   * 3ページ配置ロジックのテスト
   */
  public function testAlignment03()
  {
    $papers = $this->createTestPapers(3);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());

    $this->assertEquals(  2, $papers[1]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getY());

    $this->assertEquals(  3, $papers[1]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[1]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[1]->getRightPage()->getY());
  }

  /**
   * 4ページ配置ロジックのテスト
   */
  public function testAlignment04()
  {
    $papers = $this->createTestPapers(4);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());

    $this->assertEquals(  2, $papers[1]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getY());

    $this->assertEquals(  3, $papers[1]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[1]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[1]->getRightPage()->getY());

    $this->assertEquals(  4, $papers[0]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[0]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[0]->getLeftPage()->getY());
  }

  /**
   * 5ページ配置ロジックのテスト
   */
  public function testAlignment05()
  {
    $papers = $this->createTestPapers(5);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());

    $this->assertEquals(  2, $papers[1]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getY());

    $this->assertEquals(  3, $papers[2]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[2]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[2]->getRightPage()->getY());

    $this->assertEquals(  4, $papers[3]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getY());

    $this->assertEquals(  5, $papers[3]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[3]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[3]->getRightPage()->getY());
  }

  /**
   * 6ページ配置ロジックのテスト
   */
  public function testAlignment06()
  {
    $papers = $this->createTestPapers(6);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());

    $this->assertEquals(  2, $papers[1]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getY());

    $this->assertEquals(  3, $papers[2]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[2]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[2]->getRightPage()->getY());

    $this->assertEquals(  4, $papers[3]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getY());

    $this->assertEquals(  5, $papers[3]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[3]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[3]->getRightPage()->getY());

    $this->assertEquals(  6, $papers[2]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[2]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[2]->getLeftPage()->getY());
  }

  /**
   * 7ページ配置ロジックのテスト
   */
  public function testAlignment07()
  {
    $papers = $this->createTestPapers(7);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());

    $this->assertEquals(  2, $papers[1]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getY());

    $this->assertEquals(  3, $papers[2]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[2]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[2]->getRightPage()->getY());

    $this->assertEquals(  4, $papers[3]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getY());

    $this->assertEquals(  5, $papers[3]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[3]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[3]->getRightPage()->getY());

    $this->assertEquals(  6, $papers[2]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[2]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[2]->getLeftPage()->getY());

    $this->assertEquals(  7, $papers[1]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[1]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[1]->getRightPage()->getY());
  }

  /**
   * 8ページ配置ロジックのテスト
   */
  public function testAlignment08()
  {
    $papers = $this->createTestPapers(8);

    $this->assertEquals(  1, $papers[0]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[0]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[0]->getRightPage()->getY());

    $this->assertEquals(  2, $papers[1]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[1]->getLeftPage()->getY());

    $this->assertEquals(  3, $papers[2]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[2]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[2]->getRightPage()->getY());

    $this->assertEquals(  4, $papers[3]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[3]->getLeftPage()->getY());

    $this->assertEquals(  5, $papers[3]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[3]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[3]->getRightPage()->getY());

    $this->assertEquals(  6, $papers[2]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[2]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[2]->getLeftPage()->getY());

    $this->assertEquals(  7, $papers[1]->getRightPage()->getNumber());
    $this->assertEquals(150, $papers[1]->getRightPage()->getX());
    $this->assertEquals( 50, $papers[1]->getRightPage()->getY());

    $this->assertEquals(  8, $papers[0]->getLeftPage()->getNumber());
    $this->assertEquals( 50, $papers[0]->getLeftPage()->getX());
    $this->assertEquals( 50, $papers[0]->getLeftPage()->getY());
  }

  /**
   *
   *
   * @param int $pageCount
   */

  /**
   * テスト出力用紙の配列を生成する
   * 出力用紙のサイズは(300, 200)
   * 各ページの画像サイズは(100, 100)
   *
   * @param $pageCount
   * @return array
   */
  private function createTestPapers($pageCount)
  {
    // ページを作成
    $pages = array();

    for ($pageNumber = 1; $pageNumber <= $pageCount; ++$pageNumber) {
      $page = $this->createPageMock(100, 100);
      $page->setNumber($pageNumber); //ページ番号をセット
      $pages[] = $page;
    }

    // ページを出力用紙に配置
    $papers = $this->runAlignmentLogic($pages);

    // 全ページに下地画像モックをセット
    $this->setBaseImageMockToPapers($papers, 300, 200);

    foreach ($papers as $paper) {
      // 各ページの座標を計算
      $paper->calculatePagePositions();
    }

    return $papers;
  }

  /**
   * ページのモックを作成する
   *
   * @param int $imageWidth
   * @param int $imageHeight
   * @return \Reoring\ImageBundle\Service\imposition\ImpositionPage
   */
  private function createPageMock($imageWidth, $imageHeight)
  {
    $image = new ImagickMock();
    $image->setImageWidth($imageWidth);
    $image->setImageHeight($imageHeight);

    $page = new ImpositionPage();
    $page->setImage($image);

    return $page;
  }

  /**
   * ページ配置ロジックの実行
   *
   * @param array of ImpositionPage $pages
   * @return array of ImpositionPaper
   */
  private function runAlignmentLogic($pages)
  {
    $alignmentLogic = new ImpositionAlignmentLogic();
    $alignmentLogic->setPages($pages);
    $alignmentLogic->run();

    return $alignmentLogic->getPapers();
  }

  /**
   * 全出力用紙に下地画像のモックをセットする
   *
   * @param array of ImpositionPaper $papers
   * @param int $baseImageWidth
   * @param int $baseImageHeight
   */
  private function setBaseImageMockToPapers($papers, $baseImageWidth, $baseImageHeight)
  {
    $baseImageMock = new ImagickMock();
    $baseImageMock->setImageWidth($baseImageWidth);
    $baseImageMock->setImageHeight($baseImageHeight);

    foreach ($papers as $paper) {
      $paper->setBaseImage($baseImageMock);
    }
  }

  /**
   * 全出力用紙に配置された全ページの座標を決定してセットする
   *
   * @param array of ImpositionPaper $papers
   */
  private function calculatePositions($papers)
  {
    foreach ($papers as $paper) {
      // 各ページの座標を計算
      $paper->calculatePagePositions();
    }
  }
}