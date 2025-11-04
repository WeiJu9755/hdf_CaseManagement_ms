<?php

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;


//載入公用函數
@include_once '/website/include/pub_function.php';

//連結資料
@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction("processform");
function processform($aFormValues){

	$objResponse = new xajaxResponse();
	
	$web_id				= trim($aFormValues['web_id']);
	$auto_seq			= trim($aFormValues['auto_seq']);
	
	
	if (trim($aFormValues['construction_id']) == "")	{
		$objResponse->script("jAlert('警示', '請輸入工程名稱', 'red', '', 2000);");
		return $objResponse;
		exit;
	}
	
	SaveValue($aFormValues);
	
	$objResponse->script("setSave();");
	$objResponse->script("parent.myDraw();");

	$objResponse->script("art.dialog.tips('已存檔!',1);");
	$objResponse->script("parent.$.fancybox.close();");
	$objResponse->script("parent.eModal.close();");
		
	
	return $objResponse;
}


$xajax->registerFunction("SaveValue");
function SaveValue($aFormValues){

	$objResponse = new xajaxResponse();
	
		//進行存檔動作
		$site_db				= trim($aFormValues['site_db']);
		$auto_seq				= trim($aFormValues['auto_seq']);
		$construction_id		= trim($aFormValues['construction_id']);
		$builder_id				= trim($aFormValues['builder_id']);
		$contractor_id			= trim($aFormValues['contractor_id']);
		$contact 				= trim($aFormValues['contact']);
		//$site_location			= trim($aFormValues['site_location']);
		$county					= trim($aFormValues['county']);
		$town					= trim($aFormValues['town']);
		$zipcode				= trim($aFormValues['zipcode']);
		$address 				= htmlspecialchars(trim($aFormValues['address']), ENT_QUOTES, 'UTF-8');
		//$ContractingModel		= trim($aFormValues['ContractingModel']);
		$Handler				= trim($aFormValues['Handler']);
		$status1				= trim($aFormValues['status1']);
		$status2				= trim($aFormValues['status2']);
		//$company_id				= trim($aFormValues['company_id']);
		$memberID				= trim($aFormValues['memberID']);
		
		//存入info實體資料庫中
		$mDB = "";
		$mDB = new MywebDB();

		$Qry="UPDATE CaseManagement set
				 construction_id	= '$construction_id'
				,builder_id			= '$builder_id'
				,contractor_id		= '$contractor_id'
				,contact			= '$contact'
				,county				= '$county'
				,town				= '$town'
				,zipcode			= '$zipcode'
				,address			= '$address'
				,status1			= '$status1'
				,status2			= '$status2'
				,makeby				= '$memberID'
				,last_modify		= now()
				where auto_seq = '$auto_seq'";
				
		$mDB->query($Qry);
        $mDB->remove();

		
	return $objResponse;
}

$xajax->processRequest();



$auto_seq = $_GET['auto_seq'];
$fm = $_GET['fm'];

$mess_title = $title;

//$pro_id = "com";


$mDB = "";
$mDB = new MywebDB();
$Qry="SELECT a.*,b.employee_name,c.builder_name,d.contractor_name FROM CaseManagement a
LEFT JOIN employee b ON b.employee_id = a.Handler
LEFT JOIN builder c ON c.builder_id = a.builder_id
LEFT JOIN contractor d ON d.contractor_id = a.contractor_id
WHERE a.auto_seq = '$auto_seq'";
$mDB->query($Qry);
$total = $mDB->rowCount();
if ($total > 0) {
    //已找到符合資料
	$row=$mDB->fetchRow(2);
	$case_id = $row['case_id'];
	$region = $row['region'];
	$construction_id = $row['construction_id'];
	$builder_id = $row['builder_id'];
	$builder_name = $row['builder_name'];
	$contractor_id = $row['contractor_id'];
	$contractor_name = $row['contractor_name'];
	$contact = $row['contact'];
	$site_location = $row['site_location'];
	$county = $row['county'];
	$town = $row['town'];
	$zipcode = $row['zipcode'];
	$address = $row['address'];
	$Handler = $row['Handler'];
	$Handler_name = $row['employee_name'];
	$status1 = $row['status1'];
	$status2 = $row['status2'];
	$makeby = $row['makeby'];
	$create_date = $row['create_date'];
	$last_modify = $row['last_modify'];


	$tw_county = array('台北市','基隆市','新北市','宜蘭縣','新竹市','新竹縣','桃園市','苗栗縣','台中市','彰化縣','南投縣','嘉義市','嘉義縣','雲林縣','台南市','高雄市','屏東縣','台東縣','花蓮縣','澎湖縣','金門縣','連江縣');
	$m_county = "";
	$m_county .=  "<option value=''>請選擇</option>";
	$count_len = sizeof($tw_county);
	for ( $i = 0; $i <= $count_len-1; $i++ ) {
		$m_county .=  "<option value=\"$tw_county[$i]\" ".mySelect($tw_county[$i],$county).">$tw_county[$i]</option>";
	}
  
}

/*
//載入所屬公司
$Qry="select company_id,company_name from company order by auto_seq";
$mDB->query($Qry);
$select_company = "";
$select_company .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_company_id = $row['company_id'];
		$ch_company_name = $row['company_name'];
		$select_company .= "<option value=\"$ch_company_id\" ".mySelect($ch_company_id,$company_id).">$ch_company_id $ch_company_name</option>";
	}
}
*/
/*
//載入所有建商
$Qry="select builder_id,builder_name from builder order by auto_seq";
$mDB->query($Qry);
$select_builder = "";
$select_builder .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_builder_id = $row['builder_id'];
		$ch_builder_name = $row['builder_name'];
		$select_builder .= "<option value=\"$ch_builder_id\" ".mySelect($ch_builder_id,$builder_id).">$ch_builder_id $ch_builder_name</option>";
	}
}
*/

//載入所有上包-建商名稱
$Qry="SELECT builder_id,builder_name FROM builder ORDER BY auto_seq";
$mDB->query($Qry);
$builder_id_list = "";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_builder_id = $row['builder_id'];
		$ch_builder_name = $row['builder_name'];
		$builder_id_list .= "<option value=\"$ch_builder_id\">$ch_builder_id $ch_builder_name</option>";
	}
}

/*
//載入所有營造商
$Qry="select contractor_id,contractor_name from contractor order by auto_seq";
$mDB->query($Qry);
$select_contractor = "";
$select_contractor .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_contractor_id = $row['contractor_id'];
		$ch_contractor_name = $row['contractor_name'];
		$select_contractor .= "<option value=\"$ch_contractor_id\" ".mySelect($ch_contractor_id,$contractor_id).">$ch_contractor_id $ch_contractor_name</option>";
	}
}
*/
//載入所有上包-營造廠名稱
$Qry="SELECT contractor_id,contractor_name FROM contractor ORDER BY auto_seq";
$mDB->query($Qry);
$contractor_id_list = "";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_contractor_id = $row['contractor_id'];
		$ch_contractor_name = $row['contractor_name'];
		$contractor_id_list .= "<option value=\"$ch_contractor_id\">$ch_contractor_id $ch_contractor_name</option>";
	}
}



/*
$pro_id = "ContractingModel";
//載入承攬模式
$Qry="select caption from items where pro_id = '$pro_id' order by pro_id,orderby";
$mDB->query($Qry);
$select_ContractingModel = "";
$select_ContractingModel .= "<option></option>";

if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$ch_caption = $row['caption'];
		$select_ContractingModel .= "<option value=\"$ch_caption\" ".mySelect($ch_caption,$ContractingModel).">$ch_caption</option>";
	}
}
*/

$getsmallclass = "/smarty/templates/$site_db/$templates/sub_modal/base/pjclass_ms/getsmallclass.php";
$getmainclass = "/smarty/templates/$site_db/$templates/sub_modal/base/pjclass_ms/getmainclass.php";


$pro_id = "CaseManagement";
//載入主類別選項
$Qry="select caption from pjclass where pro_id = '$pro_id' and small_class = '0' order by orderby";
$mDB->query($Qry);
$select_status1 = "";
$select_status1 .= "<option></option>";

if ($mDB->rowCount() > 0) {
    while ($row=$mDB->fetchRow(2)) {
		$mc_caption = $row['caption'];
		$select_status1 .= "<option value=\"$mc_caption\" ".mySelect($mc_caption,$status1).">$mc_caption</option>";
	}
}
//檢查並設定細類
//先取出 caption () 的 main_class 值
$m_row = getkeyvalue2($site_db."_info","pjclass","pro_id = '$pro_id' and small_class = '0' and caption = '$status1'","main_class");
$main_class_seq = $m_row['main_class'];
//從資料庫中讀取主類別資料
$Qry="select caption from pjclass where pro_id = '$pro_id' and main_class = '$main_class_seq' and small_class <> '0' order by orderby";
$select_status2 = "";
$select_status2 .= "<option></option>";
$mDB->query($Qry);
if ($mDB->rowCount() > 0) {
	while ($row=$mDB->fetchRow(2)) {
		$sc_caption = $row['caption'];
		$select_status2 .= "<option value=\"$sc_caption\" ".mySelect($sc_caption,$status2).">$sc_caption</option>";
	}
}	


$mDB->remove();


$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="save" class="btn btn-primary" type="button" onclick="CheckValue(this.form);" style="padding: 5px 15px;"><i class="bi bi-check-circle"></i>&nbsp;存檔</button>
	<button id="cancel" class="btn btn-secondary display_none" type="button" onclick="setCancel();" style="padding: 5px 15px;"><i class="bi bi-x-circle"></i>&nbsp;取消</button>
	<button id="close" class="btn btn-danger" type="button" onclick="parent.myDraw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = 0;
	
$style_css=<<<EOT
<style>

.card_full {
    width: 100%;
	height: 100vh;
}

#full {
    width: 100%;
	height: 100vh;
}

#info_container {
	width: 900px !Important;
	margin: 0 auto !Important;
}

.field_div1 {width:200px;display: none;font-size:18px;color:#000;text-align:right;font-weight:700;padding:15px 10px 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}
.field_div2 {width:100%;max-width:650px;display: none;font-size:18px;color:#000;text-align:left;font-weight:700;padding:8px 0 0 0;vertical-align: top;display:inline-block;zoom: 1;*display: inline;}

.code_class {
	width:200px;
	text-align:right;
	padding:0 10px 0 0;
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

.code_class {
	width:auto;
	text-align:left;
	padding:0 10px 0 0;
}

</style>
EOT;

}

/*
$show_ConfirmSending = "";


	if ($ConfirmSending == "Y") {

$show_ConfirmSending=<<<EOT
				<div class="w-100 text-center">
					<button disabled type="button" class="btn btn-secondary btn-lg text-center px-5 m-2"><div class="size12 weight"><i class="bi bi-lock-fill"></i>&nbsp;已確認送出</div><div class="size08 yellow weight">$ConfirmSending_datetime</div></button>
					<button type="button" class="btn btn-warning btn-lg text-center px-5 m-2" onclick="Reduction(this.form);"><div class="size12 weight"><i class="bi bi-unlock-fill"></i>&nbsp;還原確認</div><div class="size08 black weight">(還原後，即可進行修改)</div></button>
				</div>
EOT;

	} else {

$show_ConfirmSending=<<<EOT
				<div class="w-100 text-center">
					<button id="myConfirmSending" disabled type="button" class="btn btn-danger btn-lg text-center px-4 my-2 mx-1" onclick="ConfirmSending(this.form);"><div class="size12 weight text-nowrap">填寫完畢，確認送出</div><div class="size08 yellow weight text-nowrap">(一旦送出即無法修改)</div></button>
				</div>
EOT;

	}
*/


$m_location		= "/smarty/templates/".$site_db."/".$templates;
$ajax_get_builder = $m_location."/sub_modal/project/func01/CaseManagement_ms/ajax_get_builder.php";
$ajax_get_contractor = $m_location."/sub_modal/project/func01/CaseManagement_ms/ajax_get_contractor.php";



$show_center=<<<EOT
<script src="/os/aj-address/js/aj-address.js" type="text/javascript"></script>

$style_css

<div class="card card_full">
	<div class="card-header text-bg-info">
		<div class="size14 weight float-start" style="margin-top: 5px;">
			$mess_title
		</div>
		<div class="float-end" style="margin-top: -5px;">
			$show_savebtn
		</div>
	</div>
	<div id="full" class="card-body data-overlayscrollbars-initialize">
		<div id="info_container">
			<form method="post" id="modifyForm" name="modifyForm" enctype="multipart/form-data" action="javascript:void(null);">
			<div class="w-100 mb-5">
				<div class="field_container3">
					<div>
						<div class="field_div1">狀態:</div> 
						<div class="field_div2">
							<div class="inline text-nowrap mb-1">
								(1):
								<select id="status1" name="status1" style="width:150px;" onchange="setEdit();">
									$select_status1
								</select>
							</div>
							<div class="inline text-nowrap mb-1">
								(2):
								<select id="status2" name="status2" style="width:150px;">
									$select_status2
								</select>
							</div>
						</div> 
					</div>
					<div>
						<div class="field_div2">
							<div class="my-1">
								<div class="inline code_class">區域:</div>
								<div class="inline blue weight">$region</div>
							</div>
							<div class="my-1">
								<div class="inline code_class">案件編號:</div>
								<div class="inline blue weight">$case_id</div>
							</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">工程名稱:</div> 
						<div class="field_div2">
							<input type="text" class="inputtext mb-2" id="construction_id" name="construction_id" size="20" maxlength="160" style="width:100%;max-width:450px;" value="$construction_id" onchange="setEdit();"/>
							<!--
							<select id="construction_id" name="construction_id" placeholder="請選擇工程名稱" style="width:100%;max-width:250px;">
								$select_construction
							</select>
							-->
						</div> 
					</div>
					<div>
						<div class="field_div1">上包-建商名稱:</div> 
						<div class="field_div2">
							<!--
							<select id="builder_id" name="builder_id" placeholder="請選擇上包-建商名稱" style="width:100%;max-width:250px;">
								$select_builder
							</select>
							-->
							<input list="builder_id_list" type="text" class="inputtext w-100" id="builder_id" name="builder_id" autocomplete="off" value="$builder_id" style="width:100%;max-width:250px;"/>
							<datalist id="builder_id_list">
								$builder_id_list
							</datalist>
							<div id="builder_info">$builder_name</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">上包-營造廠名稱:</div> 
						<div class="field_div2">
							<!--
							<select id="contractor_id" name="contractor_id" placeholder="請選擇上包-營造廠名稱" style="width:100%;max-width:250px;">
								$select_contractor
							</select>
							-->
							<input list="contractor_id_list" type="text" class="inputtext w-100" id="contractor_id" name="contractor_id" autocomplete="off" value="$contractor_id" style="width:100%;max-width:250px;"/>
							<datalist id="contractor_id_list">
								$contractor_id_list
							</datalist>
							<div id="contractor_info">$contractor_name</div>
						</div> 
					</div>
					<div>
						<div class="field_div1">連絡人:</div> 
						<div class="field_div2">
							<input type="text" class="inputtext mb-2" id="contact" name="contact" size="20" maxlength="160" style="width:100%;max-width:450px;" value="$contact" onchange="setEdit();"/>
						</div> 
					</div>
					<div>
						<div class="field_div1">案場位置:</div> 
						<div class="field_div2">
							<select class="input_button" id="county" name="county">$m_county</select>
							<select class="input_button" id="town" name="town"></select>
							<input readonly type="text" class="inputtext" id="zipcode" name="zipcode" style="width:100%;max-width: 80px;" value="$zipcode"/>
						</div> 
					</div>
					<div>
						<div class="field_div1"></div> 
						<div class="field_div2">
							<input type="text" class="inputtext" id="address" name="address" size="80" maxlength="240" style="width:100%;max-width:500px;" value="$address" onchange="setEdit();"/>
						</div> 
					</div>
					<!--
					<div>
						<div class="field_div1">承攬模式:</div> 
						<div class="field_div2">
							<select id="ContractingModel" name="ContractingModel" placeholder="請選擇承攬模式" style="width:100%;max-width:250px;">
								$select_ContractingModel
							</select>
						</div> 
					</div>
					<div>
						<div class="field_div1">所屬公司:</div> 
						<div class="field_div2">
							<select id="company_id" name="company_id" placeholder="請選擇所屬公司" style="width:100%;max-width:250px;">
								$select_company
							</select>
						</div> 
					</div>
					-->
					<div>
						<input type="hidden" name="fm" value="$fm" />
						<input type="hidden" name="site_db" value="$site_db" />
						<input type="hidden" name="auto_seq" value="$auto_seq" />
						<input type="hidden" name="memberID" value="$memberID" />
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<script>

function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function SaveValue(thisform) {
	xajax_SaveValue(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function setEdit() {
	$('#close', window.document).addClass("display_none");
	$('#cancel', window.document).removeClass("display_none");
}

function setCancel() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
	document.forms[0].reset();
}

function setSave() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
}


function getSelectVal(){ 
	$("option",status2).remove(); //清空原有的選項
	var main_class_val = $("#status1").val();
    $.getJSON('$getsmallclass',{main_class:main_class_val,site_db:'$site_db',pro_id:'$pro_id'},function(json){ 
        var small_class = $("#status2"); 
        var option = "<option></option>";
		small_class.append(option);
        $.each(json,function(index,array){ 
			option = "<option value='"+array['caption']+"'>"+array['caption']+"</option>"; 
            small_class.append(option); 
        }); 
    });
}

$(function(){ 
    $("#status1").change(function(){ 
        getSelectVal(); 
    }); 
});


//更新主類別
function getMainSelectVal(){ 
    $.getJSON("$getmainclass",{site_db:'$site_db',pro_id:'$pro_id'},function(json){ 
        var main_class = $("#status1"); 
		var last_option = main_class.val();
        $("option",status1).remove(); //清空原有的選項
        var option = "<option></option>";
		main_class.append(option);
        $.each(json,function(index,array){
			if (array['caption'] == last_option)
				option = "<option value='"+array['caption']+"' selected>"+array['caption']+"</option>"; 
			else
				option = "<option value='"+array['caption']+"'>"+array['caption']+"</option>"; 
            main_class.append(option); 
        }); 
    }); 
}

init_address();
set_address('$county','$town');

$(document).ready(async function() {
	//等待其他資源載入完成，此方式適用大部份瀏覽器
	await new Promise(resolve => setTimeout(resolve, 100));
	$('#status1').focus();
});



  $('#builder_id').on('input', function() {
    var builder_id = $(this).val();  // 即時取得 input 的值
    //$('#builder_info').text(builder_id);   // 顯示在畫面上
	if (builder_id !== '') {
		$.ajax({
			url: '$ajax_get_builder', // 後端 PHP 檔案
			method: 'POST',
			data: { site_db : '$site_db', builder_id: builder_id },
			dataType: 'json',
			success: function (response) {
				if (response.success) {
					$('#builder_info').text(response.builder_name);
				} else {
					$('#builder_info').text('');
				}

			},
			error: function () {
		    	$('#builder_info').text('');   // 顯示在畫面上
			}
		});

	} else {
    	$('#builder_info').text('');   // 顯示在畫面上
	}

  });

  $('#contractor_id').on('input', function() {
    var contractor_id = $(this).val();  // 即時取得 input 的值
    //$('#contractor_info').text(contractor_id);   // 顯示在畫面上
	if (contractor_id !== '') {
		$.ajax({
			url: '$ajax_get_contractor', // 後端 PHP 檔案
			method: 'POST',
			data: { site_db : '$site_db', contractor_id: contractor_id },
			dataType: 'json',
			success: function (response) {
				if (response.success) {
					$('#contractor_info').text(response.contractor_name);
				} else {
					$('#contractor_info').text('');
				}

			},
			error: function () {
		    	$('#contractor_info').text('');   // 顯示在畫面上
			}
		});

	} else {
    	$('#contractor_info').text('');   // 顯示在畫面上
	}

  });


</script>

EOT;

?>