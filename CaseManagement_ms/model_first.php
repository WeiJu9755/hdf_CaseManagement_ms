<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/".$site_db."_info_class.php");


$m_location		= "/website/smarty/templates/".$site_db."/".$templates;
$m_pub_modal	= "/website/smarty/templates/".$site_db."/pub_modal";

$sid = "";
if (isset($_GET['sid']))
	$sid = $_GET['sid'];


//程式分類
$ch = empty($_GET['ch']) ? 'default' : $_GET['ch'];
switch($ch) {
	case 'add':
		$title = "新增資料";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func01/CaseManagement_ms/CaseManagement_add.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
	case 'edit':
		$title = "編輯作業";
		$sid = "view01";
		$modal = $m_location."/sub_modal/project/func01/CaseManagement_ms/CaseManagement_modify.php";
		include $modal;
		$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
		/*
	case 'mview':
	case 'view':
		$title = "資料瀏覽";
		if (empty($sid))
			$sid = "mbpjitem";
		$modal = $m_location."/sub_modal/project/func01/CaseManagement_ms/CaseManagement_view.php";
		include $modal;
		//$smarty->assign('show_center',$show_center);
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
		*/
	default:
		if (empty($sid))
			$sid = "mbpjitem";
		$modal = $m_location."/sub_modal/project/func01/CaseManagement_ms/CaseManagement.php";
		include $modal;
		$smarty->assign('xajax_javascript', $xajax->getJavascript('/xajax/'));
		break;
};

?>