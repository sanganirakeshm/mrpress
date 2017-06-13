<?php

namespace Ld\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ld\UserBundle\Entity\PermissionCategory;
use Ld\AdminBundle\Form\Type\PermissionCategoryFormType;
use Doctrine\ORM\EntityRepository;
use \DateTime;

class PermissionCategoryController extends Controller {

    public function listAction(Request $request) {
    	
    	$user 			= $this->get('security.token_storage')->getToken()->getUser();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_cat_list');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}
        
        return $this->render('LdAdminBundle:PermissionCategory:list.html.twig');
    }
    
    //Added For Grid List
    public function listJsonAction($orderBy = "id", $sortOrder = "asc", $search = "all", $offset = 0) {
        
    	$em 			= $this->getDoctrine()->getManager();
    	$admin 			= $this->get('security.token_storage')->getToken()->getUser();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_cat_list');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}
    	    	
    	$helper 		= $this->get('grid_helper_function');
    
        $permissionCategoryColumns = array('chkid', 'id', 'name', 'status', 'action');
        
        $gridData 	= $helper->getSearchData($permissionCategoryColumns);
        
        $sortOrder 	= $gridData['sort_order'];
        $orderBy 	= $gridData['order_by'];
        
        if ($gridData['sort_order'] == '' && $gridData['order_by'] == '') {
            
            $orderBy = 'pc.id';
            $sortOrder = 'DESC';
        } else {
            
             if ($gridData['order_by'] == 'id') {
                
                $orderBy = 'pc.id';
            }

            if ($gridData['order_by'] == 'name') {
                
                $orderBy = 'pc.name';
            }            
            
            if ($gridData['order_by'] == 'status') {
            
            	$orderBy = 'pc.status';
            }
            
        }

        // Paging
        $per_page 	= $gridData['per_page'];
        $offset 	= $gridData['offset'];
      
        $data  = $em->getRepository('LdUserBundle:PermissionCategory')->getPermissionCategoryGridList($per_page, $offset, $orderBy, $sortOrder, $gridData['search_data'], $gridData['SearchType'], $helper);
      
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
    		$permissionArr = array('admin_permission_cat_add');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}       
        
        $permissionCategory = new PermissionCategory();
        $form = $this->createForm(new PermissionCategoryFormType(), $permissionCategory, array('validation_groups' => array('addPermissionCategory')));
        
        if($request->getMethod() == "POST") {
            
            $form->handleRequest($request);
            
            if($form->isValid()) {
                
                $em->persist($permissionCategory);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', "Permission category have been added successfully!");
                return $this->redirect($this->generateUrl('ld_admin_permission_category_list'));
            }
        }
        
        return $this->render('LdAdminBundle:PermissionCategory:add.html.twig', array(
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
    		$permissionArr = array('admin_permission_cat_edit');
    		$this->get('admin_permission')->isPermissionGranted($permissionArr);
    		//End Check Permission
    	}
        
        $permissionCategory = $em->getRepository('LdUserBundle:PermissionCategory')->find($id);        
        $form 		= $this->createForm(new PermissionCategoryFormType(), $permissionCategory, array('validation_groups' => array('updatePermissionCategory')));
        
        if($request->getMethod() == "POST") {
            
            $form->handleRequest($request);
            
            if($form->isValid()) {
                
                $em->persist($permissionCategory);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('success', "Permission category have been updated successfully!");
                return $this->redirect($this->generateUrl('ld_admin_permission_category_list'));
            }
        }
        
        return $this->render('LdAdminBundle:PermissionCategory:edit.html.twig', array(
                        'form' 				 => $form->createView(),
                        'permissionCategory' => $permissionCategory
        ));
    }
    
    public function updateStatusAction(Request $request)
    {
    	$em 	= $this->getDoctrine()->getManager();
    	$userGroupCode 	= $this->get('session')->get('userGroupCode');
    	//Check Permission
    	if ($userGroupCode != 'SuperAdmin') {
    		 
    		//Check Permission
    		$permissionArr = array('admin_permission_cat_activeInactive');
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
    			 
    			$updateUserStatus = $em->createQueryBuilder()->update('LdUserBundle:PermissionCategory', 'pc')
    			->set('pc.status', $status)
    			->where('pc.id IN(:Ids)')
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
    		$permissionArr = array('admin_permission_cat_delete');
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
    			 
    			$deleteUser= $em->createQueryBuilder()->update('LdUserBundle:PermissionCategory', 'pc')
				    			->set('pc.isDeleted', 1)
				    			->where('pc.id IN(:Ids)')
				    			->setParameter('Ids', $idArr)
				    			->getQuery()->execute();
    	   
    			if($deleteUser){
    
    				$response['status'] = true;
    				$response['msgType'] 	= 'success';
    				$response['msg'] = 'Permission category have been deleted successfully.';
    			}
    		}
    
    		return new Response(json_encode($response));
    	}
    }
}
