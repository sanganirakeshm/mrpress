<?php

namespace Ld\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ld\UserBundle\Entity\Permission;
use Ld\AdminBundle\Form\Type\PermissionFormType;
use Doctrine\ORM\EntityRepository;
use \DateTime;

class PermissionController extends Controller {

    public function listAction(Request $request) {
    	
    	$user 			= $this->get('security.token_storage')->getToken()->getUser();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    	
    		//Check Permission
    		$permissionArr = array('admin_permission_list');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}

    	//Check Permission
        if ($userGroupCode != 'SuperAdmin' && !($this->get('admin_permission')->checkPermission('admin_permission_list') || $this->get('admin_permission')->checkPermission('admin_permission_update') )) {
        	
            $this->get('session')->getFlashBag()->add('failure', "You are not allowed to view permission list.");
            return $this->redirect($this->generateUrl('ld_admin_dashboard'));
        }
        
        return $this->render('LdAdminBundle:Permission:list.html.twig');
    }
    
    //Added For Grid List
    public function listJsonAction($orderBy = "id", $sortOrder = "asc", $search = "all", $offset = 0) {
        
    	$em 			= $this->getDoctrine()->getManager();
    	$admin 			= $this->get('security.token_storage')->getToken()->getUser();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_list');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}
    	
    	$helper 		= $this->get('grid_helper_function');
    
        $permissionColumns = array('chkid', 'id', 'name', 'code', 'status', 'action');
        
        $gridData 	= $helper->getSearchData($permissionColumns);
        
        $sortOrder 	= $gridData['sort_order'];
        $orderBy 	= $gridData['order_by'];
        
        if ($gridData['sort_order'] == '' && $gridData['order_by'] == '') {
            
            $orderBy = 'p.id';
            $sortOrder = 'DESC';
        } else {
            
             if ($gridData['order_by'] == 'id') {
                
                $orderBy = 'p.id';
            }

            if ($gridData['order_by'] == 'name') {
                
                $orderBy = 'p.name';
            }
            
            if ($gridData['order_by'] == 'code') {
            
            	$orderBy = 'p.code';
            }
            
            if ($gridData['order_by'] == 'status') {
            
            	$orderBy = 'p.status';
            }
            
        }

        // Paging
        $per_page 	= $gridData['per_page'];
        $offset 	= $gridData['offset'];
      
        $data  = $em->getRepository('LdUserBundle:Permission')->getPermissionGridList($per_page, $offset, $orderBy, $sortOrder, $gridData['search_data'], $gridData['SearchType'], $helper);
      
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()
        );
        if (isset($data) && !empty($data)) {
            
            if (isset($data['result']) && !empty($data['result'])) {
                
                $output = array(
                    "sEcho" => intval($_GET['sEcho']),
                    "iTotalRecords" => $data['totalRecord'],
                    "iTotalDisplayRecords" => $data['totalRecord'],
                    "aaData" => array()
                );
                
                
                foreach ($data['result'] AS $resultRow) {
                                      
                    $row = array();
                    $row[] = '';
                    $row[] = $resultRow->getId();
                    $row[] = $resultRow->getName();
                    $row[] = $resultRow->getCode();
                    $row[] = ($resultRow->getStatus())?'Active':'In Active';
                    
                    $output['aaData'][] = $row;
                }
                
            }
        }

        $response = new Response(json_encode($output));
    	$response->headers->set('Content-Type', 'application/json');
    
    	return $response;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request            
     * @return type
     */
    public function addAction(Request $request) {
        
    	$em 			= $this->getDoctrine()->getManager();
    	$admin 			= $this->get('security.token_storage')->getToken()->getUser();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_add');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}
    	
        //Check Permission
        if($userGroupCode != 'SuperAdmin' && ! $this->get('admin_permission')->checkPermission('admin_permission_create')) {
        	
            $this->get('session')->getFlashBag()->add('failure', "You are not allowed to add permission.");
            return $this->redirect($this->generateUrl('ld_admin_dashboard'));
        }
        
        $permission = new Permission();
        $form = $this->createForm(new PermissionFormType(), $permission, array('validation_groups' => array('userPermission')));
        
        if($request->getMethod() == "POST") {
            
            $form->handleRequest($request);
            
            if($form->isValid()) {
                
                $em->persist($permission);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', "Permission have been added successfully!");
                return $this->redirect($this->generateUrl('ld_admin_permission_list'));
            }
        }
        
        return $this->render('LdAdminBundle:Permission:add.html.twig', array(
                        'form' => $form->createView()
        ));
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request            
     * @return type
     */
    public function editAction(Request $request, $id) {
        
    	$em 			= $this->getDoctrine()->getManager();
    	$admin 			= $this->get('security.token_storage')->getToken()->getUser();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_edit');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}    	
        
        $permission = $em->getRepository('LdUserBundle:Permission')->find($id);        
        $form 		= $this->createForm(new PermissionFormType(), $permission, array('validation_groups' => array('updatePermission')));
        
        if($request->getMethod() == "POST") {
            
            $form->handleRequest($request);
            
            if($form->isValid()) {
                
                $em->persist($permission);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', "Permission have been updated successfully!");
                return $this->redirect($this->generateUrl('ld_admin_permission_list'));
            }
        }
        
        return $this->render('LdAdminBundle:Permission:edit.html.twig', array(
                        'form' => $form->createView(),
                        'permission' => $permission
        ));
    }
    
    public function updateStatusAction(Request $request)
    {
    	$em 	= $this->getDoctrine()->getManager();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_activeInactive');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}
    
    	if($request->isXmlHttpRequest()) {
    
    		$id 	= $request->get('id');
    		$status = $request->get('status');
    		$mode	= $request->get('mode');
    		$idArr  = array();
    
    		$response = array();
    		$response['status'] 	= false;
    		$response['msgType']	= 'danger';
    		$response['msg']    	= 'Something went wrong while processing update status request!';
    
    		if ($mode == 'single') {
    
    			$idArr[] = $id;
    		}
    		if ($mode == 'all') {
    
    			$idArr = explode(',',$id);
    		}
    
    		if ($idArr) {
    
    			$updateUserStatus = $em->createQueryBuilder()->update('LdUserBundle:Permission', 'p')
    			->set('p.status', $status)
    			->where('p.id IN(:Ids)')
    			->setParameter('Ids', $idArr)
    			->getQuery()->execute();
    
    			if($updateUserStatus){
    
    				$response['status'] 	= true;
    				$response['msgType'] 	= 'success';
    				$response['msg'] 		= 'Status have been updated successfully.';
    			}
    		}
    
    		return new Response(json_encode($response));
    	}
    }
    
    public function deleteAction(Request $request)
    {
    	$em 	= $this->getDoctrine()->getManager();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_delete');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}
    
    	if($request->isXmlHttpRequest()) {
    
    		$id 	= $request->get('id');
    		$mode	= $request->get('mode');
    		$idArr  = array();
    
    		$response = array();
    		$response['status'] 	= false;
    		$response['msgType']	= 'danger';
    		$response['msg']    	= 'Something went wrong while processing delete request!';
    
    		if ($mode == 'single') {
    
    			$idArr[] = $id;
    		}
    		if ($mode == 'all') {
    
    			$idArr = explode(',',$id);
    		}
    
    		if ($idArr) {
    
    			$delete= $em->createQueryBuilder()->update('LdUserBundle:Permission', 'p')
    			->set('p.isDeleted', 1)
    			->where('p.id IN(:Ids)')
    			->setParameter('Ids', $idArr)
    			->getQuery()->execute();
    
    			if($delete){
    
    				$response['status'] = true;
    				$response['msgType'] 	= 'success';
    				$response['msg'] = 'Permission have been deleted successfully.';
    			}
    		}
    
    		return new Response(json_encode($response));
    	}
    }
}
