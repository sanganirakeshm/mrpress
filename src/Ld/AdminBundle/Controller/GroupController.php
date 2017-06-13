<?php

namespace Ld\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ld\AdminBundle\Form\Type\AdminFormType;
use Ld\UserBundle\Entity\User;
use Ld\UserBundle\Entity\Group;
use Ld\AdminBundle\Form\Type\GroupFormType;
use Ld\AdminBundle\Form\Type\GroupPermissionFormType;
use Doctrine\ORM\EntityRepository;
use \DateTime;

class GroupController extends Controller {

	public function listAction(Request $request) {
		 
		$user 			= $this->get('security.token_storage')->getToken()->getUser();
		$userGroupCode 	= $this->get('session')->get('userGroupCode');
	
		//Check Permission
		if ($userGroupCode != 'SuperAdmin') {
			 
			//Check Permission
			$permissionArr = array('admin_group_list');
			$this->get('admin_permission')->isPermissionGranted($permissionArr);
			//End Check Permission
		}
	
		return $this->render('LdAdminBundle:Group:list.html.twig');
	}
	
	//Added For Grid List
	public function listJsonAction($orderBy = "id", $sortOrder = "asc", $search = "all", $offset = 0) {
		$userGroupCode 	= $this->get('session')->get('userGroupCode');
		
		//Check Permission
		if ($userGroupCode != 'SuperAdmin') {
		
			//Check Permission
			$permissionArr = array('admin_group_list');
			$this->get('admin_permission')->isPermissionGranted($permissionArr);
			//End Check Permission
		}
	
		$em 			= $this->getDoctrine()->getManager();
		$admin 			= $this->get('security.token_storage')->getToken()->getUser();
		$userGroupCode 	= $this->get('session')->get('userGroupCode');
		$helper 		= $this->get('grid_helper_function');
	
		$groupColumns = array('chkid', 'id', 'name', 'code', 'status', 'action');
	
		$gridData 	= $helper->getSearchData($groupColumns);
	
		$sortOrder 	= $gridData['sort_order'];
		$orderBy 	= $gridData['order_by'];
	
		if ($gridData['sort_order'] == '' && $gridData['order_by'] == '') {
	
			$orderBy = 'g.id';
			$sortOrder = 'DESC';
		} else {
	
			if ($gridData['order_by'] == 'id') {
	
				$orderBy = 'g.id';
			}
	
			if ($gridData['order_by'] == 'name') {
	
				$orderBy = 'g.name';
			}
			
			if ($gridData['order_by'] == 'code') {
			
				$orderBy = 'g.code';
			}
	
			if ($gridData['order_by'] == 'status') {
	
				$orderBy = 'g.status';
			}
	
		}
	
		// Paging
		$per_page 	= $gridData['per_page'];
		$offset 	= $gridData['offset'];
	
		$data  = $em->getRepository('LdUserBundle:Group')->getGroupGridList($per_page, $offset, $orderBy, $sortOrder, $gridData['search_data'], $gridData['SearchType'], $helper);
	
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
			$permissionArr = array('admin_group_add');
			$this->get('admin_permission')->isPermissionGranted($permissionArr);
			//End Check Permission
		}
		 
		//Check Permission
		if($userGroupCode != 'SuperAdmin' && ! $this->get('admin_permission')->checkPermission('admin_group_create')) {
			 
			$this->get('session')->getFlashBag()->add('failure', "You are not allowed to add group.");
			return $this->redirect($this->generateUrl('ld_admin_dashboard'));
		}
	
		$group = new Group();
		$form = $this->createForm(new GroupFormType(), $group, array('validation_groups' => array('addGroup')));
	
		if($request->getMethod() == "POST") {
	
			$form->handleRequest($request);
	
			if($form->isValid()) {
	
				$em->persist($group);
				$em->flush();
	
				$this->get('session')->getFlashBag()->add('success', "Group have been added successfully!");
				return $this->redirect($this->generateUrl('ld_admin_group_list'));
			}
		}
	
		return $this->render('LdAdminBundle:Group:add.html.twig', array(
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
			$permissionArr = array('admin_group_edit');
			$this->get('admin_permission')->isPermissionGranted($permissionArr);
			//End Check Permission
		}
		
		$group 	= $em->getRepository('LdUserBundle:Group')->find($id);
		$form 	= $this->createForm(new GroupFormType(), $group, array('validation_groups' => array('updateGroup')));
	
		if($request->getMethod() == "POST") {
	
			$form->handleRequest($request);
	
			if($form->isValid()) {
	
				$em->persist($group);
				$em->flush();
	
				$this->get('session')->getFlashBag()->add('success', "Group have been updated successfully!");
				return $this->redirect($this->generateUrl('ld_admin_group_list'));
			}
		}
	
		return $this->render('LdAdminBundle:Group:edit.html.twig', array(
				'form' 	=> $form->createView(),
				'group' => $group
		));
	}
	
	public function permissionAction(Request $request, $id) {

		$admin = $this->get('security.token_storage')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$userGroupCode 	= $this->get('session')->get('userGroupCode');
		
		//Check Permission
		if ($userGroupCode != 'SuperAdmin') {
		
			//Check Permission
			$permissionArr = array('admin_group_permission');
			$this->get('admin_permission')->isPermissionGranted($permissionArr);
			//End Check Permission
		}
		
		$group = $em->getRepository('LdUserBundle:Group')->find($id);
		$categories = $em->getRepository('LdUserBundle:PermissionCategory')->findAll();
		
		$form = $this->createForm(new GroupPermissionFormType(), $group);
		
		if($request->getMethod() == "POST") {
		
			$form->handleRequest($request);
		
			if($form->isValid()) {
		
				$em->persist($group);
				$em->flush();
		
				$this->get('session')->getFlashBag()->add('success', "Group permissions updated successfully!");
				return $this->redirect($this->generateUrl('ld_admin_group_list'));
			}
		
		}
		
		return $this->render('LdAdminBundle:Group:permission.html.twig', array(
				'form' => $form->createView(),
				'group' => $group,
				'categories' => $categories,
		));
		
		echo "ddddd";exit;
	}
	
	public function updateStatusAction(Request $request)
	{
		$em 	= $this->getDoctrine()->getManager();
		$userGroupCode 	= $this->get('session')->get('userGroupCode');
		
		//Check Permission
		if ($userGroupCode != 'SuperAdmin') {
		
			//Check Permission
			$permissionArr = array('admin_group_activeInactive');
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
	
				$updateUserStatus = $em->createQueryBuilder()->update('LdUserBundle:Group', 'g')
				->set('g.status', $status)
				->where('g.id IN(:Ids)')
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
			$permissionArr = array('admin_group_delete');
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
	
				$delete= $em->createQueryBuilder()->update('LdUserBundle:Group', 'g')
				->set('g.isDeleted', 1)
				->where('g.id IN(:Ids)')
				->setParameter('Ids', $idArr)
				->getQuery()->execute();
	
				if($delete){
	
					$response['status'] = true;
					$response['msgType'] 	= 'success';
					$response['msg'] = 'Group have been deleted successfully.';
				}
			}
	
			return new Response(json_encode($response));
		}
	}           
}
