<?php

namespace Ld\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
	public function indexAction()
	{
		return $this->render('LdUserBundle:Dashboard:index.html.twig', array(
				// ...
		));
	}
}
