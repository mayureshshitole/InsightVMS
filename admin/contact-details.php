<?php
session_start();
error_reporting(0);
include('includes/config.php');
// if sesion blank or unauthorized access this will redirect to admin login page
if(strlen($_SESSION['userlogin'])==0):
header('location:index.php');
// if session is valid
else:
// for updating read status of contact form
if(isset($_GET['cid']))
{
$formid=$_GET['cid'];
$isread=1;
$sql=" update  tblcontactdata set Is_Read=:isread where id=:cfid";
$query = $dbh->prepare($sql);
$query->bindParam(':cfid',$formid,PDO::PARAM_STR);
$query->bindParam(':isread',$isread,PDO::PARAM_STR);
$query->execute();
}

// Admin remark Insertion
if(isset($_POST['submit']))
{
$formid=$_GET['cid'];
$remark=$_POST['remark'];
$phoneno = $_POST['$result->PhoneNumber'];
$sql="INSERT INTO   tbladminremarks(contactFormId,adminRemark) VALUES(:cfid,:adminrmrk)";
$query = $dbh->prepare($sql);
$query->bindParam(':cfid',$formid,PDO::PARAM_STR);
$query->bindParam(':adminrmrk',$remark,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
  $msg="Remark added successfully";
  $var = "Insight Business Machines pvt ltd Your Visting Request has been approved";
	
    $fields = array(
    "sender_id" => "FSTSMS",
    "message" => "$var",
    "language" => "english",
    "route" => "p",
    "numbers" => "$phoneno",
    "flash" => "0"
);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.fast2sms.com/dev/bulk",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($fields),
  CURLOPT_HTTPHEADER => array(
    "authorization: YGcX7DQAjemprngC2WPFJZ6wxEfVhH8kRiKyaLSN5lBIo4Ouz0opFXEnlq87tUbSWIjgTyzGAcHa0B96",
    "accept: */*",
    "cache-control: no-cache",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo "<script>alert('Thank you for filling the form! Have a great day!');</script>";
}
}
 else {
    $error="Something went wrong please try again.";
}
}



?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>

  <title>Insight VMS Admin | Contact Details
  </title>

  <link rel="shortcut icon" type="image/x-icon" href="../../../app-assets/images/ico/favicon.ico">
  <link rel="shortcut icon" href="app-assets/images/insight_logo_mini.ico">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
  rel="stylesheet">
  <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css"
  rel="stylesheet">
  <!-- BEGIN VENDOR CSS-->
  <link rel="stylesheet" type="text/css" href="app-assets/css/vendors.css">
<link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu-modern.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/palette-gradient.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/pages/invoice.css">
<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body class="vertical-layout vertical-menu-modern 2-columns   menu-expanded fixed-navbar"
data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
<?php include('includes/header.php');?>
<?php include('includes/leftbar.php');?>
  <div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
          <h3 class="content-header-title mb-0 d-inline-block">Contact Details</h3>
          <div class="row breadcrumbs-top d-inline-block">
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a>
                </li>
                </li>
                <li class="breadcrumb-item active">Contact Details</li>
              </ol>
            </div>
          </div>
        </div>
    
      </div>
      <div class="content-body">
        <section class="card">
          <div id="invoice-template" class="card-body">

<?php if($msg){?>
<div class="alert alert-success mb-2" role="alert">
<strong>Well done!</strong><?php echo htmlentities($msg);?>
</div>    
<?php } ?>       
<!-- for Error message -->
<?php if($error) {?>
<div class="alert alert-danger mb-2" role="alert">
<strong>Oh snap!</strong> <?php echo htmlentities($error);?>
</div>
<?php } ?>

<?php 
$fid=intval($_GET['cid']);// Getting contact form id
$sql = "SELECT tblcontactdata.FullName,tblcontactdata.PhoneNumber,tblcontactdata.UserIp,tblcontactdata.EmailId,tblcontactdata.Temperature,tblcontactdata.Oxygen,tblcontactdata.Subject,tblcontactdata.Message,tblcontactdata.PostingDate,tblcontactdata.id,tbladminremarks.adminRemark,tbladminremarks.remarkDate from tblcontactdata left join  tbladminremarks on  tbladminremarks.contactFormId=tblcontactdata.id where tblcontactdata.id=:fid";
$query = $dbh -> prepare($sql);
$query-> bindParam(':fid', $fid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
$phoneno = $result->PhoneNumber;
if($query->rowCount() > 0):
foreach($results as $result):
       ?>

 
            <div id="invoice-customer-details" class="row pt-2">
         
              <div class="col-md-6 col-sm-12 text-center text-md-left">
                <ul class="px-0 list-unstyled">
                  <li class="text-bold-800"><?php echo htmlentities($result->FullName);?> (IP Address : <?php echo htmlentities($result->UserIp);?>)</li>
                  <li><b>Phone Number : </b><?php echo htmlentities($result->PhoneNumber);?></li>
                  <li><b>Email id : </b><?php echo htmlentities($result->EmailId);?></li>
				  <li><b>Temperature : </b><?php echo htmlentities($result->Temperature);?></li>
				  <li><b>Oxygen : </b><?php echo htmlentities($result->Oxygen);?></li>
                  <li><b>Visiting Date and Time : </b><?php echo htmlentities($result->PostingDate);?></li>
                </ul>
              </div>
            </div>
            <!--/ Invoice Customer Details -->
            <!-- Invoice Items Details -->
            <div id="invoice-items-details" class="pt-2">
              <div class="row">
                <div class="table-responsive col-sm-12">
                  <table class="table">
                
                    <tbody>
                      <tr>
                        <th scope="row">Person to Meet</th>
                        <td><p><?php echo htmlentities($result->Subject);?></p>
                        </td>
                      </tr>
                     <tr>
                        <th scope="row">Purpose of Visiting </th>
                        <td><p><?php echo htmlentities($result->Message);?></p>
                        </td>
                      </tr>
					  
					  <tr>
                        <th scope="row">Phone No </th>
                        <td><p><?php echo htmlentities($result->PhoneNumber);?></p>
                        </td>
                      </tr>
					  
                  <tr>
                        <th scope="row">Admin Remark </th>
                        <td><p><?php echo htmlentities($phoneno);?>
                        <br />
                          <b>Remark Date :</b> <?php echo htmlentities($result->remarkDate);?>
                        </p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php
                      $cnt++;
endforeach;
endif;
                      ?>
            </div>
            <!-- Invoice Footer -->
            <div id="invoice-footer">
              <div class="row">
         
                <div class="col-md-5 col-sm-12 text-center">
                 <button type="button" class="btn btn-outline-warning block btn-lg" data-toggle="modal"
                          data-target="#warning">
                            Add Remark
                          </button>
			     
    <div class="modal fade text-left" id="warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel12"
                          aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header bg-warning white">
                                  <h3 class="modal-title white" id="myModalLabel12"><i class="la la-file"></i> Add admin remark here</h3>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <h4><i class="la la-arrow-right"></i> Remark</h4>
                                  <form name="remark" method="post">
                                  <p>
                        <textarea class="form-control" name="remark" rows="6" required></textarea>
                                  </p>
                              
                                  <hr>
                            
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-outline-warning">Save changes</button>  </form>
                                </div>
                              </div>
                            </div>
                          </div>







                </div>
              </div>
            </div>
            <!--/ Invoice Footer -->
          </div>
        </section>
      </div>
    </div>
  </div>
<?php include('includes/footer.php');?>
  <!-- BEGIN VENDOR JS-->
  <script src="app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
  <script src="app-assets/js/core/app-menu.js" type="text/javascript"></script>
  <script src="app-assets/js/core/app.js" type="text/javascript"></script>
  <script src="app-assets/js/scripts/customizer.js" type="text/javascript"></script>

</body>
</html>
<?php endif;?>