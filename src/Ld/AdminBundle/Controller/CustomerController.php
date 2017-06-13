<?php

namespace Ld\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ld\AdminBundle\Form\Type\CustomerFormType;
use Ld\AdminBundle\Form\Type\ChangePasswordFormType;
use Ld\UserBundle\Entity\User;

class CustomerController extends Controller
{
	
    public function listAction()
    {
    	//Check Permission
    	$permissionArr = array('admin_cust_list');
    	$this->get('admin_permission')->isPermissionGranted($permissionArr);
    	//End Check Permission
    	
    	$em = $this->getDoctrine()->getManager();
    	$admin = $this->get('security.token_storage')->getToken()->getUser();
    	
    	return $this->render('LdAdminBundle:Customer:list.html.twig', array('admin' => $admin));
    }

    public function listJsonAction($orderBy = "id", $sortOrder = "asc", $search = "all", $offset = 0) {
    	//Check Permission
    	$permissionArr = array('admin_cust_list');
    	$this->get('admin_permission')->isPermissionGranted($permissionArr);
    	//End Check Permission
    	
    	$em 	= $this->getDoctrine()->getManager();
    	$admin 	= $this->get('security.token_storage')->getToken()->getUser();
    	$helper = $this->get('grid_helper_function');
    	
    	$adminColumns = array('chkid','id','firstname','lastname','username','email','enabled','action');
    	     	
    	$gridData = $helper->getSearchData($adminColumns);
    
    	$sortOrder 	= $gridData['sort_order'];
    	$orderBy 	= $gridData['order_by'];
    
    	if ($gridData['sort_order'] == '' && $gridData['order_by'] == '') {
    
    		$orderBy = 'u.id';
    		$sortOrder = 'DESC';
    	} else {
    		
    		if ($gridData['order_by'] == 'id') {
    
    			$orderBy = 'u.id';
    		}
    		if ($gridData['order_by'] == 'firstname') {
    		
    			$orderBy = 'u.firstname';
    		}
    		if ($gridData['order_by'] == 'lastname') {
    		
    			$orderBy = 'u.lastname';
    		} 
    		if ($gridData['order_by'] == 'username') {
    		
    			$orderBy = 'u.username';
    		}   
    	}
    
    	// Paging
    	$per_page 	= $gridData['per_page'];
    	$offset 	= $gridData['offset'];
    
    	$data  = $em->getRepository('LdUserBundle:User')->getCustomerGridList($per_page, $offset, $orderBy, $sortOrder, $gridData['search_data'], $gridData['SearchType'], $helper,$admin);
    
    	$output = array(
    			"sEcho" 				=> intval($_GET['sEcho']),
    			"iTotalRecords" 		=> 0,
    			"iTotalDisplayRecords" 	=> 0,
    			"aaData" 				=> array()
    	);
    	if (isset($data) && !empty($data)) {
    
    		if (isset($data['result']) && !empty($data['result'])) {
    
    			$output = array(
    					"sEcho" 				=> intval($_GET['sEcho']),
    					"iTotalRecords" 		=> $data['totalRecord'],
    					"iTotalDisplayRecords" 	=> $data['totalRecord'],
    					"aaData" 				=> array()
    			);
    
    			foreach ($data['result'] AS $resultRow) {
    				 
    				$row = array();
    				$row[] = '';
    				$row[] = $resultRow->getId();
    				$row[] = $resultRow->getFirstname();
    				$row[] = $resultRow->getLastname();
    				$row[] = $resultRow->getUsername();
    				$row[] = $resultRow->getEmail();
    				$row[] = ($resultRow->isEnabled())?'Active':'In Active';
        
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
    
    	//Check Permission
    	$permissionArr = array('admin_cust_add');
    	$this->get('admin_permission')->isPermissionGranted($permissionArr);
    	//End Check Permission
    	
    	$admin = $this->get('security.context')->getToken()->getUser();
    
    	$em = $this->getDoctrine()->getManager();
    
    	$form = $this->createForm(new CustomerFormType($admin), new User(), array('validation_groups' => array('userUpdate')));
    
    	if ($request->getMethod() == "POST") {
    
    		$form->handleRequest($request);
    		
    		if ($form->isValid()) {
    
    			$objUser = $form->getData();
    			$objUser->setRoles(array('ROLE_USER'));
    			$em->persist($objUser);
    			$em->flush();
       
    			$this->get('session')->getFlashBag()->add('success', "Customer data have been added successfully!");
    			return $this->redirect($this->generateUrl('ld_admin_customer_list'));
    		} else {
    			$this->get('session')->getFlashBag()->add('failure', "Customer data have not been updated successfully!");
    		}
    	}
    
    	return $this->render('LdAdminBundle:Customer:add.html.twig', array('form' => $form->createView(), 'user' => $admin));    
    }
    
    public function editAction(Request $request, $id) {
    	
    	//Check Permission
    	$permissionArr = array('admin_cust_edit');
    	$this->get('admin_permission')->isPermissionGranted($permissionArr);
    	//End Check Permission
    	
    	$admin = $this->get('security.context')->getToken()->getUser();
    
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('LdUserBundle:User')->find($id);    
    	
    	if (!$user) {
    		$this->get('session')->getFlashBag()->add('failure', "Customer does not exist.");
    		return $this->redirect($this->generateUrl('ld_admin_customer_list'));
    	}
    
    	$form = $this->createForm(new CustomerFormType($admin), $user, array('validation_groups' => array('userUpdate')));
    	$changePasswordForm = $this->createForm(new ChangePasswordFormType($admin), $user, array('validation_groups' => array('ChangePassword')));
    
    	if ($request->getMethod() == "POST") {
    
    		if ($request->request->has($form->getName())) {
    
    			$username = $user->getUsername();
    			$form->handleRequest($request);
    
    			if ($form->isValid()) {
    
    				$objUser = $form->getData();    				
    				$em->persist($objUser);
    				$em->flush();
        				
    				$this->get('session')->getFlashBag()->add('success', "Customer data have been updated successfully!");
    				return $this->redirect($this->generateUrl('ld_admin_customer_list'));
    			} else {
    				
    				$this->get('session')->getFlashBag()->add('failure', "Customer data have not been updated successfully!");    				
    			} 
    		}
    
    		if ($request->request->has($changePasswordForm->getName())) {
    			
    			$changePasswordForm->handleRequest($request);
    			
    			if ($changePasswordForm->isValid()) {
    
    				$userManager = $this->get('fos_user.user_manager');
    				$userManager->updateUser($user);
    
    				
    				$this->get('session')->getFlashBag()->add('success', "Password have been updated successfully!");
    				return $this->redirect($this->generateUrl('ld_admin_customer_list'));
    			} else {
    				
    				$this->get('session')->getFlashBag()->add('failure', "Password have not been updated successfully!");
    				/*$errors = array();
    				foreach ($changePasswordForm->getErrors() as $key => $error) {
    					$errors[] = $error->getMessage();
    				}
    				echo '<pre>';print_r($errors);exit;*/
    			}
    		}
    	}
    
    	return $this->render('LdAdminBundle:Customer:edit.html.twig', array(
    			'form' => $form->createView(),
    			'user' => $user,
    			'changePasswordForm' => $changePasswordForm->createView(),
    	));
    
    }
    
    public function updateStatusAction(Request $request)
    {
    	//Check Permission
    	$permissionArr = array('admin_cust_activeInactive');
    	$this->get('admin_permission')->isPermissionGranted($permissionArr);
    	//End Check Permission
    	
    	$em 	= $this->getDoctrine()->getManager();
    	
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
    			
	    		$updateUserStatus = $em->createQueryBuilder()->update('LdUserBundle:User', 'u')
	    								->set('u.enabled', $status)
	    								->where('u.id IN(:userId)')
	    								->setParameter('userId', $idArr)
	    								->getQuery()->execute();
	    		
	    		if($updateUserStatus){
	    			
	    			$response['status'] 	= true;
	    			$response['msgType'] 	= 'success';
	    			$response['msg'] 		= 'Status updated successfully.';
	    		}
    		}
    		
    		return new Response(json_encode($response));
    	}    	
    }
    
    public function deleteAction(Request $request)
    {
    	//Check Permission
    	$permissionArr = array('admin_cust_delete');
    	$this->get('admin_permission')->isPermissionGranted($permissionArr);
    	//End Check Permission
    	
    	$em 	= $this->getDoctrine()->getManager();
    	
    	if($request->isXmlHttpRequest()) {
    		
    		$id 	= $request->get('id');
    		$status = $request->get('status');
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
    			
    			$deleteUser= $em->createQueryBuilder()->update('LdUserBundle:User', 'u')
	    								->set('u.isDeleted', 1)
	    								->where('u.id IN(:userId)')
	    								->setParameter('userId', $idArr)
	    								->getQuery()->execute();
	    		
	    		if($deleteUser){
	    			
	    			$response['status'] = true;
	    			$response['msgType'] 	= 'success';
	    			$response['msg'] = 'Customer deleted successfully.';
	    		}
    		}
    		
    		return new Response(json_encode($response));
    	}
    }
}
