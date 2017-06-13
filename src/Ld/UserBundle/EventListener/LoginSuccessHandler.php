<?php

namespace Ld\UserBundle\EventListener;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface {

    protected $router;
    protected $security;
    protected $session;
    protected $container;
    private $em;
    

    public function __construct(Router $router, SecurityContext $security, Session $session, Doctrine $doctrine, $container) {
        $this->router = $router;
        $this->security = $security;
        $this->session = $session;
        $this->em = $doctrine->getManager();
        $this->container = $container;
        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        
        
        if ($this->security->isGranted('ROLE_ADMIN')) {
        
            $this->session->getFlashBag()->add('danger', 'Invalid credentials.');
            $response = new RedirectResponse($this->router->generate('fos_user_security_logout'));
            
            
        } else {
            
            $user = $this->security->getToken()->getUser();
            $userMacAddress = $this->em->getRepository('DhiUserBundle:UserSetting')->findOneBy(array('user' => $user));
            
            if($userMacAddress && $userMacAddress->getMacAddress() != "") {
                
                $this->session->set('maxMacAddress', $userMacAddress->getMacAddress());
            }
            else {
                
                $userMacAddress = $this->em->getRepository('DhiAdminBundle:Setting')->findOneBy(array('name' => 'mac_address'));
                
                $this->session->set('maxMacAddress', $userMacAddress->getValue());
            }
            
            
            $response = new RedirectResponse($this->router->generate('dhi_user_account'));
        }

        return $response;
    }

}
