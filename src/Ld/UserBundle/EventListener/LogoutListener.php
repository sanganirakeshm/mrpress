<?php

namespace Ld\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; 

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \DateTime;
use Symfony\Component\Routing\Router;
use Ld\UserBundle\Entity\UserActivityLog;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class LogoutListener implements LogoutSuccessHandlerInterface
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
    private $router;
        
	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(Router $router, SecurityContext $securityContext, Doctrine $doctrine)
	{ 
		$this->securityContext 	= $securityContext;
		$this->em              	= $doctrine->getManager();
		$this->router 			= $router;
                
	}
	
	/**
	 * 
	 * @param request $request
	 */
	public function onLogoutSuccess(Request $request)
	{ 
            if( ! $this->securityContext->getToken()) {
                if(strpos($request->getPathInfo(), '/admin/') !== false) { 
                    $response = new RedirectResponse($this->router->generate('admin_login'));
                } else {
                    $response = new RedirectResponse($this->router->generate('fos_user_security_login'));
                }
                return $response;
            }
            $admin = $this->securityContext->getToken()->getUser();
            
            
            if (
                    $admin->hasRole('ROLE_SUPER_ADMIN')
                    || $admin->hasRole('ROLE_HELPDESK')
                    || $admin->hasRole('ROLE_CASHIER')
                    || $admin->hasRole('ROLE_MANAGER')
                    || $admin->hasRole('ROLE_ADMIN')

                ) {
                
                $this->em->persist($admin);
                //$this->em->flush();
                
                // set routing for adminpanel
                
                $route = $this->router->generate('admin_login');
            } else {
                
                
                // set routing for frontend
                $route = $this->router->generate('fos_user_security_login');
            }
            
            
            $response = new RedirectResponse($route);

            return $response;
	}
       
}


