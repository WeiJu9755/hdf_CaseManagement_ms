<?php

session_start();
$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");

function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
//   	$objResponse->alert("formData: " . print_r($aFormValues, true));
//   	$objResponse->alert("formData: " . print_r($_POST, true));
	
	$powerkey = trim($aFormValues['powerkey']);
	$super_admin = trim($aFormValues['super_admin']);
	$admin_readonly = trim($aFormValues['admin_readonly']);
	
	$bError = false;
	
	/*
	$today = date("Y-m-d");


	if (trim($aFormValues['fm']) == "CaseManagement") {

		if (($powerkey == "A") || (($super_admin=="Y") && ($admin_readonly <> "Y"))) {

		} else {
			if (trim($aFormValues['CaseManagement_date']) == "")	{
				$objResponse->script("jAlert('警示', '請輸入日期', 'red', '', 2000);");
				return $objResponse;
				exit;
			}

			if (trim($aFormValues['CaseManagement_date']) < $today)	{
				$objResponse->script("jAlert('警示', '日期不可小於今天', 'red', '', 2000);");
				return $objResponse;
				exit;
			}
		}

	} else {
		if (trim($aFormValues['CaseManagement_date']) == "")	{
			$objResponse->script("jAlert('警示', '請輸入日期', 'red', '', 2000);");
			return $objResponse;
			exit;
		}

		if (trim($aFormValues['CaseManagement_date']) <= $today)	{
			$objResponse->script("jAlert('警示', '日期必須大於今天', 'red', '', 2000);");
			return $objResponse;
			exit;
		}
	}
	*/
	if (trim($aFormValues['year']) == "")	{
		$objResponse->script("jAlert('警示', '請輸入年分', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	
	if (trim($aFormValues['region']) == "")	{
		$objResponse->script("jAlert('警示', '請選擇區域', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	if (trim($aFormValues['construction_id']) == "")	{
		$objResponse->script("jAlert('警示', '請輸入工程名稱', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	$year = trim($aFormValues['year']);
	$region = trim($aFormValues['region']);
	$m_region = "M";
	if ($region == "北部") {
		$m_region = "N";
	} else if ($region == "中部") {
		$m_region = "M";
	} else if ($region == "南部") {
		$m_region = "S";
	}
	

	//自動編碼 case_id
	//案件編號：西元年後2碼+H+區域+流水號3碼，ex.24HN001→可否自動編碼
	//$o_id = substr(date("Y"),-2,2)."H".$m_region;
	$o_id = substr($year, -2, 2);

	$mDB = "";
	$mDB = new MywebDB();
	
	//取得最後的群組代號
	$Qry = "SELECT case_id FROM CaseManagement WHERE SUBSTRING(case_id,1,2) = '$o_id' ORDER BY auto_seq DESC LIMIT 0,1";
	$mDB->query($Qry);
	if ($mDB->rowCount() > 0) {
		$row=$mDB->fetchRow(2);
		$temp_case_id = $row['case_id'];
		$str3 = (int)substr($temp_case_id,-3,3);
		$num = $str3+1;
		$filled_int = sprintf("%03d", $num);
		$new_case_id = $o_id."H".$m_region.$filled_int;
	} else {
		$new_case_id = $o_id."H".$m_region."001";
	}
	
	if (!$bError) {
		$fm					= trim($aFormValues['fm']);
		$site_db			= trim($aFormValues['site_db']);
		$construction_id	= trim($aFormValues['construction_id']);
		$member_no			= trim($aFormValues['member_no']);

		$company_id			= "93530861";		//禾登豐工程股份有限公司

		
		//存入實體資料庫中
		$mDB = "";
		$mDB = new MywebDB();
	  
		$Qry="insert into CaseManagement (case_id,region,construction_id,company_id,makeby,create_date,last_modify) values ('$new_case_id','$region','$construction_id','$company_id','$member_no',now(),now())";
		$mDB->query($Qry);
		//再取出auto_seq
		$Qry="select auto_seq from CaseManagement where case_id = '$new_case_id' order by auto_seq desc limit 0,1";
		$mDB->query($Qry);
		if ($mDB->rowCount() > 0) {
			//已找到符合資料
			$row=$mDB->fetchRow(2);
			$auto_seq = $row['auto_seq'];
		}

		/*
		//取得 上工時間 預設值
		$settings_row = getkeyvalue2($site_db."_info","settings","auto_seq = '1'","def_attendance_start");
		if (!empty($settings_row['def_attendance_start']))
			$def_attendance_start = $settings_row['def_attendance_start'];
		else
			$def_attendance_start = "";

		$mDB2 = "";
		$mDB2 = new MywebDB();

		//工單新增完成，自動將所有團隊人員加入至工單中
		//$Qry = "SELECT * FROM team_member where team_id = '$team_id'";
		$Qry = "SELECT a.*,b.construction_id FROM team_member a
				LEFT JOIN team b ON b.team_id = a.team_id
				WHERE a.team_id = '$team_id'";
		$mDB->query($Qry);
		if ($mDB->rowCount() > 0) {
			while ($row=$mDB->fetchRow(2)) {
				$employee_id = $row['employee_id'];
				$construction_id = $row['construction_id'];
	
				//先檢查同一天團隊人員是否已有資料(避免重複)
				$Qry2 = "SELECT a.auto_seq FROM CaseManagement_member a
					LEFT JOIN CaseManagement b ON b.case_id = a.case_id
					WHERE b.CaseManagement_date = '$CaseManagement_date' AND a.employee_id = '$employee_id'";
				$mDB2->query($Qry2);
				if ($mDB2->rowCount() > 0) {
					//已存在則不作任何處理
				} else {
					$Qry2 = "insert into CaseManagement_member (case_id,employee_id,attendance_status,construction_id,attendance_start) values ('$new_case_id','$employee_id','正常出勤','$construction_id','$def_attendance_start')";
					$mDB2->query($Qry2);
				}
				
			}
		}
	
        $mDB2->remove();
		*/

        $mDB->remove();

		if (!empty($auto_seq)) {
			$objResponse->script("myDraw();");
			//$objResponse->script("art.dialog.tips('已新增，請繼續輸入其他資料...',2);");
			$objResponse->script("window.location='/?ch=edit&auto_seq=$auto_seq&fm=$fm';");
			//$objResponse->script("parent.$.fancybox.close();");
		} else {
			//$objResponse->script("art.dialog.alert('發生不明原因的錯誤，資料未新增，請再試一次!');");
			$objResponse->script("parent.$.fancybox.close();");
		}
	};
	
	return $objResponse;	
}

$xajax->processRequest();

$fm = $_GET['fm'];
$t = $_GET['t'];


if ($fm == "CaseManagement") {
	$default_day = date("Y-m-d");
} else {
	$default_day = date("Y-m-d", strtotime("+1 day"));
}

$mess_title = $title;

$super_admin = "N";
$mem_row = getkeyvalue2('memberinfo','member',"member_no = '$memberID'",'admin,admin_readonly');
$super_admin = $mem_row['admin'];
$admin_readonly = $mem_row['admin_readonly'];


/*
//從 $memberID 取得員工所屬公司
$employee_row = getkeyvalue2($site_db."_info","employee","member_no = '$memberID'","company_id,team_id");
$company_id =$employee_row['company_id'];
$team_id =$employee_row['team_id'];

//取得公司名稱
$company_row = getkeyvalue2($site_db."_info","company","company_id = '$company_id'","company_name");
$company_name =$company_row['company_name'];

$cando = true;
if (empty($company_id)) {
	$cando = false;
	$sid = "view01";
	$show_center = mywarning2("很抱歉! 您的員工身分未指定公司，無法進行本項作業。",'<button type="button" class="btn btn-primary" onclick="parent.$.fancybox.close();">關閉</button>');
} else if (empty($team_id)) {
	$cando = false;
	$sid = "view01";
	$show_center = mywarning2("很抱歉! 您的員工身分未指定團隊，無法進行本項作業。",'<button type="button" class="btn btn-primary" onclick="parent.$.fancybox.close();">關閉</button>');
}
*/

$cando = true;

if ($cando == true) {



	$mDB = "";
	$mDB = new MywebDB();
	
	$pro_id = "region";
	//載入區域
	$Qry="select caption from items where pro_id = '$pro_id' order by pro_id,orderby";
	$mDB->query($Qry);
	$select_region = "";
	$select_region .= "<option></option>";
	
	if ($mDB->rowCount() > 0) {
		while ($row=$mDB->fetchRow(2)) {
			$ch_caption = $row['caption'];
			$select_region .= "<option value=\"$ch_caption\">$ch_caption</option>";
		}
	}
	
	/*
	//載入所有工地
	$Qry="select construction_id,construction_site,engineering_name from construction order by auto_seq";
	$mDB->query($Qry);
	$select_construction = "";
	$select_construction .= "<option></option>";
	
	if ($mDB->rowCount() > 0) {
		while ($row=$mDB->fetchRow(2)) {
			$ch_construction_id = $row['construction_id'];
			$ch_construction_site = $row['construction_site'];
			$ch_engineering_name = $row['engineering_name'];
			$select_construction .= "<option value=\"$ch_construction_id\" ".mySelect($ch_construction_id,$construction_id).">$ch_construction_id $ch_construction_site (工程名稱：{$ch_engineering_name})</option>";
		}
	}
	*/

	$mDB->remove();




if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;

$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 1000px !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:150px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:800px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.maxwidth {
    width: 100%;
    max-width: 250px;
}
</style>
EOT;

} else {
	$isMobile = 1;
$style_css=<<<EOT
<style>

.card_full {
    width: 100vw;
	height: 100vh;
}

#full {
    width: 100vw;
	height: 100vh;
}

#info_container {
	width: 100% !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:15px 10px 0 0;vertical-align: top;}
.field_div2 {width:100%;display: block;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 10px 0 0;vertical-align: top;}

.maxwidth {
    width: 100%;
}
</style>
EOT;

}

$this_year = date('Y');

$show_center=<<<EOT
$style_css
<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start">
			$mess_title
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="addForm" name="addForm" enctype="multipart/form-data" action="javascript:void(null);">
				<div class="field_container3">
					<div>
						<div class="field_div1">案件編號:</div> 
						<div class="field_div2 blue02" style="padding-top: 15px;">
							系統自動產生
						</div>
					</div>
					<div>
					<div class="field_div1">年度:</div> 
						<div class="field_div2 pt-3">
							<input type="number" 
								class="inputtext" 
								id="year" 
								name="year" 
								min="2000" 
								max="2099" 
								step="1" 
								value="$this_year" 
								placeholder="請輸入年度" 
								style="width:100%;max-width:150px;"/>
						</div>
					</div>
					<div>
						<div class="field_div1">區域:</div> 
						<div class="field_div2 pt-3">
							<select id="region" name="region" placeholder="請選擇區域" style="width:100%;max-width:150px;">
								$select_region
							</select>
						</div> 
					</div>
					<div>
						<div class="field_div1">工程名稱:</div> 
						<div class="field_div2 pt-3">
							<input type="text" class="inputtext" id="construction_id" name="construction_id" size="20" maxlength="160" style="width:100%;max-width:450px;"/>
							<!--
							<select id="construction_id" name="construction_id" placeholder="請選擇工程名稱" style="width:100%;max-width:550px;">
								$select_construction
							</select>
							-->
						</div> 
					</div>
				</div>
				<div class="form_btn_div mt-5">
					<input type="hidden" name="fm" value="$fm" />
					<input type="hidden" name="powerkey" value="$powerkey" />
					<input type="hidden" name="super_admin" value="$super_admin" />
					<input type="hidden" name="admin_readonly" value="$admin_readonly" />
					<input type="hidden" name="site_db" value="$site_db" />
					<input type="hidden" name="templates" value="$templates" />
					<input type="hidden" name="member_no" value="$memberID" />
					<button class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 10px;margin-right: 10px;"><i class="bi bi-check-lg green"></i>&nbsp;確定新增</button>
					<button class="btn btn-danger" type="button" onclick="parent.$.fancybox.close();" style="padding: 10px;"><i class="bi bi-power"></i>&nbsp關閉</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('addForm'));
	thisform.submit();
}

var myDraw = function(){
	var oTable;
	oTable = parent.$('#db_table').dataTable();
	oTable.fnDraw(false);
}
	
</script>
EOT;

}

?>