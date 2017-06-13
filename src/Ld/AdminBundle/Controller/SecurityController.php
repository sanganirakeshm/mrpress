<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ld\AdminBundle\Controller;

#use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends BaseController
{
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $role = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        if(is_object($user)) {
        	
            $request = $this->container->get('request');
            $roles = $user->getRoles();            
            if(strpos($request->getPathInfo(), '/admin/') !== false && $roles['0'] != 'ROLE_USER') {
            	 
            	$url = $this->container->get('router')->generate('ld_admin_dashboard');
                
                return new RedirectResponse($url);
            } 
        } 
        
        return $this->container->get('templating')->renderResponse('LdAdminBundle:Security:login.html.twig', $data);
    }
}
