<?php

namespace Reoring\ImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
  public function indexAction($name)
  {
    return $this->render('ReoringImageBundle:Default:index.html.twig', array('name' => $name));
  }
}