<?php

namespace Ld\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminPermissionController extends Controller
{
    protected $container;
    
    public function __construct($container) {
    
        $this->container = $container;
    }
    
    public function checkPermission($permission)
    {
        $permissions = $this->container->get('session')->get('permissions');
        
        if(in_array($permission, $permissions))
            return true;
        else
            return false;
    }

    public function isPermissionGranted($permissionArr) {
    	
    	$isAccess = false;
    	if ($permissionArr) {
    		
    		foreach ($permissionArr as $permission) {
    			
    			if($this->checkPermission($permission)) {
    				
    				$isAccess = true;
    			}
    		}
    	}
    	if (!$isAccess) {
    		
    		throw $this->createAccessDeniedException('You cannot access this page!');
    		//$this->get('session')->getFlashBag()->add('failure', "You are not allowed to view admin list.");
    		//return new RedirectResponse($this->generateUrl('ld_admin_dashboard'));exit;
    	}    	
    }
}
