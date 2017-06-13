<?php

namespace Ld\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Ld\UserBundle\Entity\User;
use \DateTime;
/**
 * Listener responsible to change the redirection at the end of the password resetting
 */

class LoginListener {

    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;
    
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;
    
    private $router;
    
    protected $container;
    


    /**
     * Constructor
     * 
     * @param SecurityContext $securityContext
     * @param Doctrine        $doctrine
     */
    public function __construct(Router $router, SecurityContext $securityContext, Doctrine $doctrine, Session $session, $container) {
        
    	$this->securityContext = $securityContext;
        $this->em = $doctrine->getManager();
        $this->session = $session;
        $this->router = $router;
        $this->router = $router;
        $this->container = $container;                      
    }

    /**
     * Do the magic.
     * 
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
    	
        $request = $event->getRequest();
        
        //$response = new RedirectResponse($this->router->generate('fos_user_security_login'));
        
        if(strpos($request->getPathInfo(), '/admin/') !== false) {
            
            if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                
                $admin = $event->getAuthenticationToken()->getUser();
            
                $admin->setIsloggedin(true);
                $this->em->persist($admin);
                $this->em->flush();
                
                
                $response = new RedirectResponse($this->router->generate('ld_admin_dashboard'));
            } else {
                //$this->session->getFlashBag()->add('failure', 'You are not authrize to login.');
                $response = new RedirectResponse($this->router->generate('admin_login'));
            }
        } else { 
            if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            	
                //$this->session->getFlashBag()->add('failure', 'You are not authrize to login.');
                $response = new RedirectResponse($this->router->generate('fos_user_security_logout'));
                //die('admin from frontend');
            } else {
            	
                $response = new RedirectResponse($this->router->generate('ld_user_account'));
                
                $user      = $event->getAuthenticationToken()->getUser();
                
                $this->em->persist($user);
                $this->em->flush();                               
            }
        }
        
        return $response;
    }
}

?>
