<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex">

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>CRM - BankSathi</title>
    <script src="{{ asset('crm/assets/scripts/jquery.min.js') }}"></script>
	<!-- Main Styles -->
	<link rel="stylesheet" href="{{ asset('crm/assets/styles/style.min.css') }}">

	<!-- Material Design Icon -->
	<link rel="stylesheet" href="{{ asset('crm/assets/fonts/material-design/css/materialdesignicons.css') }}">

	<!-- mCustomScrollbar -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/mCustomScrollbar/jquery.mCustomScrollbar.min.css') }}">

	<!-- Waves Effect -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/waves/waves.min.css') }}">

	<!-- Sweet Alert -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/sweet-alert/sweetalert.css') }}">

	<!-- Percent Circle -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/percircle/css/percircle.css') }}">

	<!-- Chartist Chart -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/chart/chartist/chartist.min.css') }}">

	<!-- FullCalendar -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/fullcalendar/fullcalendar.min.css') }}">
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/fullcalendar/fullcalendar.print.css') }}" media='print'>

	<!-- Color Picker -->
    <link rel="stylesheet" href="{{ asset('crm/assets/color-switcher/color-switcher.min.css') }}">

    <link rel="stylesheet" href="{{ asset('crm/assets/plugin/form-wizard/prettify.css') }}">
    <link rel="stylesheet" href="{{ asset('crm/assets/plugin/lightview/css/lightview/lightview.css') }}">

    <!-- Datepicker -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/datepicker/css/bootstrap-datepicker.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('crm/assets/plugin/select2/css/select2.min.css') }}">

    <!-- FlexDatalist -->
<link rel="stylesheet" href="{{ asset('crm/assets/plugin/flexdatalist/jquery.flexdatalist.min.css') }}">
<!-- Dropify -->
<link rel="stylesheet" href="{{ asset('crm/assets/plugin/dropify/css/dropify.min.css') }}">
<!-- Data Tables -->
<link rel="stylesheet" href="{{ asset('crm/assets/plugin/datatables/media/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('crm/assets/plugin/datatables/extensions/Responsive/css/responsive.bootstrap.min.css') }}">
<!-- Flex Datalist -->
<script src="{{ asset('crm/assets/plugin/flexdatalist/jquery.flexdatalist.min.js') }}"></script>
<!-- alert sweet -->
<script src="{{ asset('crm/assets/plugin/sweet-alert/sweetalert.min.js') }}"></script>
{{-- @livewireStyles --}}

<style>
	.css-btn {
	  border: 2px solid black;
	  border-radius: 5px;
	  background-color: rgba(255,255,255,0.1);
	  color: black;
	  padding: 7px 14px;
	  font-size: 16px;
	  cursor: pointer;
	}
	
	/* Green */
	.success {
	  border-color: #4CAF50;
	  color: green;
	}
	
	.success:hover {
	  /* background-color: #4CAF50; */
	  color: white;
	}
	
	/* Blue */
	.info {
	  border-color: #2196F3;
	  color: dodgerblue
	}
	
	.info:hover {
	  background: #2196F3;
	  color: white;
	}
	
	/* Orange */
	.warning {
	  border-color: #ff9800;
	  color: orange;
	}
	
	.warning:hover {
	  background: #ff9800;
	  color: white;
	}
	
	/* Red */
	.danger {
	  border-color: #f44336;
	  color: red
	}
	
	.danger:hover {
	  background: #f4433636;
	  color: white;
	}
	
	/* Gray */
	.default {
	  border-color: #e7e7e7;
	  color:#ffffff;
	}
	
	.default:hover {
	  background: rgba(255,255,255,0.4);
	}
	</style>

</head>

<body>
<div class="main-menu">
	<header class="header">
		<a href="{{ route('crm.dashboard') }}" class="logo">BankSathi</a>
		<button type="button" class="button-close fa fa-times js__menu_close"></button>
	</header>
	
	<?php 
		$sessionArray = Session::get('sessionArray');
		$user_type = $sessionArray['userDetails']['user_type'];
		if(isset($sessionArray['userDetails'])) {
	?>
	<?php 
	$sessionArray = Session::get('sessionArray');
	$sessionArray = isset($sessionArray) ? $sessionArray : [0] ;

	$adminModule = [];
	$advisorModule = [];
	$loanModule = [];
	$insuranceModule = [];
	$creditCardModule = [];
	$socialCardModule = [];
	$socialBannerModule = [];
	$notificationModule = [];
	$payoutModule = [];

	if (isset($sessionArray['permissions'])) {
		foreach ($sessionArray['permissions'] as $key => $value) {
			if ($value['module']=='admin') {
				$adminModule = explode(",", $value['permissions']);
			} elseif ($value['module']=='advisor') {
				$advisorModule = explode(",", $value['permissions']);
			} elseif ($value['module']=='loan') {
				$loanModule = explode(",", $value['permissions']);
			}  elseif ($value['module']=='insurance') {
				$insuranceModule = explode(",", $value['permissions']);
			} elseif ($value['module']=='creditCard') {
				$creditCardModule = explode(",", $value['permissions']);
			} elseif ($value['module']=='socialCard') {
				$socialCardModule = explode(",", $value['permissions']);
			} elseif ($value['module']=='socialBanner') {
				$socialBannerModule = explode(",", $value['permissions']);
			} elseif ($value['module']=='notification') {
				$notificationModule = explode(",", $value['permissions']);
			} else {
				$payoutModule = explode(",", $value['permissions']);
			}
		}
	} else {
		$adminModule = [0];
		$advisorModule = [0];
		$loanModule = [0];
		$insuranceModule = [0];
		$creditCardModule = [0];
		$socialCardModule = [0];
		$socialBannerModule = [0];
		$notificationModule = [0];
		$payoutModule = [0];
	}
	


	if ($sessionArray['userDetails']['user_type']!=1) {
		if ((in_array(1, $loanModule)==false) && (in_array(1, $insuranceModule)==false) && (in_array(1, $creditCardModule)==false)) { 
			$leadSection = 0;
		} else {
			$leadSection = 1;
		}
	} else {
		$leadSection = 1;
	}

	if ($sessionArray['userDetails']['user_type']==1) {
        $permissions = 1;
    } else {
        $permissions = count($sessionArray['permissions']);
    }
?>

	<?php if ($permissions!=0) { ?>
	<div class="content">
		<div class="navigation">
        <h5 class="title">Users</h5>

			<ul class="menu js__accordion">
				
				<li>
					<a class="waves-effect" href="{{route('crm.dashboard')}}"><i class="menu-icon fa fa-dashboard"></i><span>Dashboard</span></a>
				</li>

				<?php if(($user_type==1)) { ?>
				<li>
					<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-briefcase"></i><span>Admin Management</span><span class="menu-arrow fa fa-angle-down"></span></a>
					<ul class="sub-menu js__content">
                    <li><a href="{{ route('admins') }}">Admin User List</a></li>
                    <li><a href="{{ route('deleted-admin') }}">Deleted Admin User</a></li>
					<li><a href="{{ route('add-admin') }}">Add Admin User</a></li>
					</ul>
				</li>
				<?php } ?>
		

				<?php 
					if($user_type!=10) {
						if (in_array(1, $advisorModule)) { 
				?>
						<li>
							<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-credit-card"></i><span>Advisor Management</span><span class="menu-arrow fa fa-angle-down"></span></a>
							<ul class="sub-menu js__content">
							<li><a href="{{ route('crm.users') }}">Advisor List</a></li>
							<li><a href="{{ route('crm.deleted') }}">Deleted Advisor</a></li>
							<li><a href="{{ route('crm.add-user') }}">Add Advisor</a></li>
							</ul>
						</li>
				<?php 
						} 
					} else {
						?>
						<li>
							<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-credit-card"></i><span>Advisor Management</span><span class="menu-arrow fa fa-angle-down"></span></a>
							<ul class="sub-menu js__content">
							<li><a href="{{ route('crm.users') }}">Advisor List</a></li>
							<li><a href="{{ route('crm.deleted') }}">Deleted Advisor</a></li>
							<li><a href="{{ route('crm.add-user') }}">Add Advisor</a></li>
							</ul>
						</li>
						<?php
					}
				?>
            </ul>

			<?php
				if ($leadSection==1) {
					?>
					<h5 class="title">Leads</h5>
					<?php
				}
			?>

			<ul class="menu js__accordion">
				<?php 
				if($user_type!=1) {
					if (in_array(1, $loanModule)) { 
						?>
						<li>
							<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-file-pdf-o"></i><span>Loan</span><span class="menu-arrow fa fa-angle-down"></span></a>
							<ul class="sub-menu js__content">
							<li><a href="{{ route('loan-pl') }}">Personal Loan</a></li>
							<li><a href="{{ route('loan-bl') }}">Business Loan</a></li>
							<li><a href="{{ route('loan-uv') }}">Used Vehicle Loan</a></li>
							</ul>
						</li>
				<?php 
					} 
				} else {
					?>
					<li>
						<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-file-pdf-o"></i><span>Loan</span><span class="menu-arrow fa fa-angle-down"></span></a>
						<ul class="sub-menu js__content">
						<li><a href="{{ route('loan-pl') }}">Personal Loan</a></li>
						<li><a href="{{ route('loan-bl') }}">Business Loan</a></li>
						<li><a href="{{ route('loan-uv') }}">Used Vehicle Loan</a></li>
						</ul>
					</li>
				<?php
				}
				?>

				<?php 
				if($user_type!=1) {
					if (in_array(1, $insuranceModule)) { 
				?>
					<li>
						<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-newspaper-o"></i><span>Insurance</span><span class="menu-arrow fa fa-angle-down"></span></a>
						<ul class="sub-menu js__content">
						<li><a href="{{ route('vehicle-ins') }}">Vehicle Insurance</a></li>
						<li><a href="{{ route('health-ins') }}">Health Insurance</a></li>
						<li><a href="{{ route('life-ins') }}">Life Insurance</a></li>
						<li><a href="{{ route('term-ins') }}">Term Insurance</a></li>
						<li><a href="{{ route('covid-ins') }}">Covid-19 Insurance</a></li>
						</ul>
					</li>
				<?php 
					}
				} else {
					?>
					<li>
						<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-newspaper-o"></i><span>Insurance</span><span class="menu-arrow fa fa-angle-down"></span></a>
						<ul class="sub-menu js__content">
						<li><a href="{{ route('vehicle-ins') }}">Vehicle Insurance</a></li>
						<li><a href="{{ route('health-ins') }}">Health Insurance</a></li>
						<li><a href="{{ route('life-ins') }}">Life Insurance</a></li>
						<li><a href="{{ route('term-ins') }}">Term Insurance</a></li>
						<li><a href="{{ route('covid-ins') }}">Covid-19 Insurance</a></li>
						</ul>
					</li>
					<?php
				}
				?>

				<?php 
				if($user_type!=1) {
					if (in_array(1, $creditCardModule)) { 
				?>
                <li>
					<a class="waves-effect parent-item" href="{{ route('credit-card') }}"><i class="menu-icon fa fa-credit-card"></i><span>Credit Card</span></a>
				</li>
				<?php 
					} 
				} else {
					?>
				<li>
					<a class="waves-effect parent-item" href="{{ route('credit-card') }}"><i class="menu-icon fa fa-credit-card"></i><span>Credit Card</span></a>
				</li>
					<?php
				} 
				?>
				{{-- <li>
					<a class="waves-effect parent-item" href="{{ route('pr-card') }}"><i class="menu-icon mdi mdi-equal-box"></i><span>Promotional Cards</span></a>
				</li> --}}
			</ul>
            <h5 class="title">Others</h5>
			<ul class="menu js__accordion">
				<?php 
				if($user_type!=1) {
					if (in_array(1, $socialCardModule)) { 
						?>
				<li>
					<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-image"></i><span>Social Cards</span><span class="menu-arrow fa fa-angle-down"></span></a>
					<ul class="sub-menu js__content">
						<?php if (in_array(3, $socialCardModule)) { ?>
							<li><a href="{{ route('pr-card') }}">Add Card</a></li>
						<?php } ?>
						<?php if (in_array(2, $socialCardModule)) { ?>
							<li><a href="{{ route('crm-pr-cards') }}">View Cards</a></li>
						<?php } ?>
					</ul>
				</li>
				<?php 
					} 
				} else {
					?>
				<li>
					<a class="waves-effect parent-item js__control" href="#"><i class="menu-icon fa fa-image"></i><span>Social Cards</span><span class="menu-arrow fa fa-angle-down"></span></a>
					<ul class="sub-menu js__content">
						<li><a href="{{ route('pr-card') }}">Add Card</a></li>
						<li><a href="{{ route('crm-pr-cards') }}">View Cards</a></li>
					</ul>
				</li>
					<?php
				}	
				?>

				<?php 
				if($user_type!=1) {
					if (in_array(1, $socialBannerModule)) { 
						?>
				<li>
					<a class="waves-effect parent-item js__control" href="#">
						<i class="menu-icon fa fa-credit-card"></i>
						<span>Social Banners</span><span class="menu-arrow fa fa-angle-down"></span></a>
						<ul class="sub-menu js__content">
							<?php if (in_array(3, $socialBannerModule)) { ?>
								<li><a href="{{ route('pr-banner') }}">Add Banner</a></li>
							<?php } ?>
							<?php if (in_array(2, $socialBannerModule)) { ?>
								<li><a href="{{ route('crm-pr-banners') }}">View Banners</a></li>
							<?php } ?>
						</ul>
				</li>
				<?php 
					}
				} else {
					?>
					<li>
						<a class="waves-effect parent-item js__control" href="#">
							<i class="menu-icon fa fa-credit-card"></i>
							<span>Social Banners</span><span class="menu-arrow fa fa-angle-down"></span></a>
							<ul class="sub-menu js__content">
								<li><a href="{{ route('pr-banner') }}">Add Banner</a></li>
								<li><a href="{{ route('crm-pr-banners') }}">View Banners</a></li>
							</ul>
					</li>
					<?php
				}
				?>

				<?php 
				if($user_type!=1) {
					if (in_array(1, $notificationModule)) { 
						?>
				<li>
					<a class="waves-effect parent-item js__control" href="#">
						<i class="menu-icon fa fa-bell"></i>
						<span>Notifications</span><span class="menu-arrow fa fa-angle-down"></span>
					</a>
					<ul class="sub-menu js__content">
						<li><a href="{{ route('notification') }}">Add Notification</a></li>
						<li><a href="{{ route('crm-notification') }}">View Notifications</a></li>
					</ul>
				</li>
				<?php 
					}
				} else {
				?>
				<li>
					<a class="waves-effect parent-item js__control" href="#">
						<i class="menu-icon fa fa-bell"></i>
						<span>Notifications</span><span class="menu-arrow fa fa-angle-down"></span>
					</a>
					<ul class="sub-menu js__content">
						<li><a href="{{ route('notification') }}">Add Notification</a></li>
						<li><a href="{{ route('crm-notification') }}">View Notifications</a></li>
					</ul>
				</li>
				<?php
				}
				?>

				
				<?php 
				if($user_type!=1) {
					if (in_array(1, $payoutModule)) { 
						?>
				<li>
					<a class="waves-effect parent-item js__control" href="#">
						<i class="menu-icon fa fa-file-text" aria-hidden="true"></i>
						<span>Payouts</span><span class="menu-arrow fa fa-angle-down"></span>
					</a>
					<ul class="sub-menu js__content">
						<?php if (in_array(3, $payoutModule)) { ?>
							<li><a href="{{ route('crm-payout') }}">Add Payout</a></li>
						<?php } ?>
						<?php if (in_array(2, $payoutModule)) { ?>
							<li><a href="{{ route('crm-payout') }}">View Payout</a></li>
						<?php } ?>
						<li><a href="{{ route('crm.importpayout') }}">Import Payout</a></li>
					</ul>
				</li> 
				<?php 
					}
				} else {
				?>
				<li>
					<a class="waves-effect parent-item js__control" href="#">
						<i class="menu-icon fa fa-file-text" aria-hidden="true"></i>
						<span>Payouts</span><span class="menu-arrow fa fa-angle-down"></span>
					</a>
					<ul class="sub-menu js__content">
						<li><a href="{{ route('crm-payout') }}">Add Payout</a></li>
						<li><a href="{{ route('crm-payout') }}">View Payout</a></li>
						<li><a href="{{ route('crm.importpayout') }}">Import Payout</a></li>
						<li><a href="{{ route('crm.comparesheets') }}">Compare Sheets</a></li>
					</ul>
				</li> 
				<?php
				}
				?>

			<?php 
				if(isset($sessionArray['userDetails'])) {
					if($sessionArray['userDetails']['user_type']==1) { 
					?>
				<li>
					<a class="waves-effect parent-item" href="{{ route('crm-permission') }}">
						<i class="menu-icon fa fa-cogs"></i>
						<span>Permissions</span>
					</a>
				</li>
				<?php 
					}
				} 
			?>
				<li>
					<a class="waves-effect parent-item" href="{{ route('crm-database') }}">
						<i class="menu-icon fa fa-database"></i>
						<span>Compare Databases</span>
					</a>
				</li>
				<br><br>
			</ul>
		</div>

	</div>
	<?php 
		} 
	?>

	<?php } ?>
</div>

<div class="fixed-navbar">
	<div class="pull-left">
		<button type="button" class="menu-mobile-button glyphicon glyphicon-menu-hamburger js__menu_mobile"></button>
		<h1 class="page-title">Home</h1>
	</div>
	<div class="pull-right">
		<div class="ico-item">
			<a href="#" class="ico-item fa fa-search js__toggle_open" data-target="#searchform-header"></a>
			<form action="#" id="searchform-header" class="searchform js__toggle"><input type="search" placeholder="Search..." class="input-search"><button class="fa fa-search button-search" type="submit"></button></form>
		</div>
		<div class="ico-item fa fa-arrows-alt js__full_screen"></div>
		<div class="ico-item toggle-hover js__drop_down ">
			<span class="fa fa-th js__drop_down_button"></span>
			<div class="toggle-content">
				<ul>
					<li><a href="#"><i class="fa fa-github"></i><span class="txt">Github</span></a></li>
					<li><a href="#"><i class="fa fa-bitbucket"></i><span class="txt">Bitbucket</span></a></li>
					<li><a href="#"><i class="fa fa-slack"></i><span class="txt">Slack</span></a></li>
					<li><a href="#"><i class="fa fa-dribbble"></i><span class="txt">Dribbble</span></a></li>
					<li><a href="#"><i class="fa fa-amazon"></i><span class="txt">Amazon</span></a></li>
					<li><a href="#"><i class="fa fa-dropbox"></i><span class="txt">Dropbox</span></a></li>
				</ul>
				<a href="#" class="read-more">More</a>
			</div>

		</div>

		<a href="#" class="ico-item fa fa-envelope notice-alarm js__toggle_open" data-target="#message-popup"></a>
		<a href="#" class="ico-item pulse"><span class="ico-item fa fa-bell notice-alarm js__toggle_open" data-target="#notification-popup"></span></a>
		<div class="ico-item">
			<img src="http://placehold.it/80x80" alt="" class="ico-img">
			<ul class="sub-ico-item">
				<li><a href="{{route('crm.edit-user',auth("admin")->user()->id)}}">Settings</a></li>
				<li><a id="pass_allert" href="{{route('crm.change-pass')}}">Change Password</a></li>
            <li><a class="allert" data-href="{{route('crm.logout')}}" href="javascript:;">Log Out</a></li>
            {{-- <li><a href="{{route('crm.logout')}}">Log Out</a></li> --}}
			</ul>
		</div>
	</div>
</div>


{{-- <div id="notification-popup" class="notice-popup js__toggle" data-space="50">
	<h2 class="popup-title">Your Notifications</h2>
	<!-- /.popup-title -->
	<div class="content">
		<ul class="notice-list">
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">John Doe</span>
					<span class="desc">Like your post: “Contact Form 7 Multi-Step”</span>
					<span class="time">10 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Anna William</span>
					<span class="desc">Like your post: “Facebook Messenger”</span>
					<span class="time">15 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar bg-warning"><i class="fa fa-warning"></i></span>
					<span class="name">Update Status</span>
					<span class="desc">Failed to get available update data. To ensure the please contact us.</span>
					<span class="time">30 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/128x128" alt=""></span>
					<span class="name">Jennifer</span>
					<span class="desc">Like your post: “Contact Form 7 Multi-Step”</span>
					<span class="time">45 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Michael Zenaty</span>
					<span class="desc">Like your post: “Contact Form 7 Multi-Step”</span>
					<span class="time">50 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Simon</span>
					<span class="desc">Like your post: “Facebook Messenger”</span>
					<span class="time">1 hour</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar bg-violet"><i class="fa fa-flag"></i></span>
					<span class="name">Account Contact Change</span>
					<span class="desc">A contact detail associated with your account has been changed.</span>
					<span class="time">2 hours</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Helen 987</span>
					<span class="desc">Like your post: “Facebook Messenger”</span>
					<span class="time">Yesterday</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/128x128" alt=""></span>
					<span class="name">Denise Jenny</span>
					<span class="desc">Like your post: “Contact Form 7 Multi-Step”</span>
					<span class="time">Oct, 28</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Thomas William</span>
					<span class="desc">Like your post: “Facebook Messenger”</span>
					<span class="time">Oct, 27</span>
				</a>
			</li>
		</ul>
		<!-- /.notice-list -->
		<a href="#" class="notice-read-more">See more messages <i class="fa fa-angle-down"></i></a>
	</div>
	<!-- /.content -->
</div> --}}
<!-- /#notification-popup -->

{{-- <div id="message-popup" class="notice-popup js__toggle" data-space="50">
	<h2 class="popup-title">Recent Messages<a href="#" class="pull-right text-danger">New message</a></h2>
	<!-- /.popup-title -->
	<div class="content">
		<ul class="notice-list">
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">John Doe</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">10 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Harry Halen</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">15 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Thomas Taylor</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">30 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/128x128" alt=""></span>
					<span class="name">Jennifer</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">45 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/80x80" alt=""></span>
					<span class="name">Helen Candy</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">45 min</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/128x128" alt=""></span>
					<span class="name">Anna Cavan</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">1 hour ago</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar bg-success"><i class="fa fa-user"></i></span>
					<span class="name">Jenny Betty</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">1 day ago</span>
				</a>
			</li>
			<li>
				<a href="#">
					<span class="avatar"><img src="http://placehold.it/128x128" alt=""></span>
					<span class="name">Denise Peterson</span>
					<span class="desc">Amet odio neque nobis consequuntur consequatur a quae, impedit facere repellat voluptates.</span>
					<span class="time">1 year ago</span>
				</a>
			</li>
		</ul>
		<!-- /.notice-list -->
		<a href="#" class="notice-read-more">See more messages <i class="fa fa-angle-down"></i></a>
	</div>
	<!-- /.content -->
</div> --}}
<!-- /#message-popup -->
{{-- <div id="color-switcher">
	<div id="color-switcher-button" class="btn-switcher">
		<div class="inside waves-effect waves-circle waves-light">
			<i class="ico fa fa-gear"></i>
		</div>
		<!-- .inside waves-effect waves-circle -->
	</div>
	<!-- .btn-switcher -->
	<div id="color-switcher-content" class="content">
		<a href="#" data-color="red" class="item js__change_color"><span class="color" style="background-color: #f44336;"></span><span class="text">Red</span></a>
		<a href="#" data-color="violet" class="item js__change_color"><span class="color" style="background-color: #673ab7;"></span><span class="text">Violet</span></a>
		<a href="#" data-color="dark-blue" class="item js__change_color"><span class="color" style="background-color: #3f51b5;"></span><span class="text">Dark Blue</span></a>
		<a href="#" data-color="blue" class="item js__change_color active"><span class="color" style="background-color: #304ffe;"></span><span class="text">Blue</span></a>
		<a href="#" data-color="light-blue" class="item js__change_color"><span class="color" style="background-color: #2196f3;"></span><span class="text">Light Blue</span></a>
		<a href="#" data-color="green" class="item js__change_color"><span class="color" style="background-color: #4caf50;"></span><span class="text">Green</span></a>
		<a href="#" data-color="yellow" class="item js__change_color"><span class="color" style="background-color: #ffc107;"></span><span class="text">Yellow</span></a>
		<a href="#" data-color="orange" class="item js__change_color"><span class="color" style="background-color: #ff5722;"></span><span class="text">Orange</span></a>
		<a href="#" data-color="chocolate" class="item js__change_color"><span class="color" style="background-color: #795548;"></span><span class="text">Chocolate</span></a>
		<a href="#" data-color="dark-green" class="item js__change_color"><span class="color" style="background-color: #263238;"></span><span class="text">Dark Green</span></a>
		<span id="color-reset" class="btn-restore-default js__restore_default">Reset</span>
	</div>
	<!-- /.content -->
</div> --}}
<!-- #color-switcher -->
<div id="wrapper">
	<div class="main-content">

        @yield('content')

{{-- {{$slot}} --}}
		<!-- /.row -->
		<footer class="footer">
			<ul class="list-inline">
				<li><?php echo date("Y");?> © BankSathi.com</li>
				<li><a href="#">Privacy</a></li>
				<li><a href="#">Terms</a></li>
				<li><a href="#">Help</a></li>
			</ul>
		</footer>
	</div>
	<!-- /.main-content -->
</div>
<!--/#wrapper -->
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="{{ asset('crm/assets/script/html5shiv.min.js') }}"></script>
		<script src="{{ asset('crm/assets/script/respond.min.js') }}"></script>
	<![endif]-->
	<!--
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<script src="{{ asset('crm/assets/scripts/modernizr.min.js') }}"></script>
	<script src="{{ asset('crm/assets/plugin/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('crm/assets/plugin/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js') }}"></script>
	<script src="{{ asset('crm/assets/plugin/nprogress/nprogress.js') }}"></script>

	<script src="{{ asset('crm/assets/plugin/waves/waves.min.js') }}"></script>
	<!-- Full Screen Plugin -->
	<script src="{{ asset('crm/assets/plugin/fullscreen/jquery.fullscreen-min.js') }}"></script>

	<!-- Google Chart sandy commented-->
	{{-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js') }}"></script> --}}

	<!-- chart.js Chart -->
	<script src="{{ asset('crm/assets/plugin/chart/chartjs/Chart.bundle.min.js') }}"></script>
	<script src="{{ asset('crm/assets/scripts/chart.chartjs.init.min.js') }}"></script>

	<!-- FullCalendar -->
	<script src="{{ asset('crm/assets/plugin/moment/moment.js') }}"></script>
	<script src="{{ asset('crm/assets/plugin/fullcalendar/fullcalendar.min.js') }}"></script>
	<script src="{{ asset('crm/assets/scripts/fullcalendar.init.js') }}"></script>

	<!-- Sparkline Chart -->
	<script src="{{ asset('crm/assets/plugin/chart/sparkline/jquery.sparkline.min.js') }}"></script>
	<script src="{{ asset('crm/assets/scripts/chart.sparkline.init.min.js') }}"></script>

	<script src="{{ asset('crm/assets/scripts/main.min.js') }}"></script>
    <script src="{{ asset('crm/assets/color-switcher/color-switcher.min.js') }}"></script>

    <!-- Form Wizard -->
<script src="{{ asset('crm/assets/plugin/form-wizard/prettify.js') }}"></script>
<script src="{{ asset('crm/assets/plugin/form-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
<script src="{{ asset('crm/assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('crm/assets/scripts/form.wizard.init.min.js') }}"></script>
<!-- Validator -->
<script src="{{ asset('crm/assets/plugin/validator/validator.min.js') }}"></script>


<!-- Datepicker -->
<script src="{{ asset('crm/assets/plugin/datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Demo Scripts -->
{{-- <script src="{{ asset('crm/assets/scripts/form.demo.min.js') }}"></script> --}}

<!-- Select2 -->
<script src="{{ asset('crm/assets/plugin/select2/js/select2.min.js') }}"></script>

<!-- Dropify -->
<script src="{{ asset('crm/assets/plugin/dropify/js/dropify.min.js') }}"></script>
<!-- Data Tables -->
<script src="{{ asset('crm/assets/plugin/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('crm/assets/plugin/datatables/media/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('crm/assets/plugin/datatables/extensions/Responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('crm/assets/plugin/datatables/extensions/Responsive/js/responsive.bootstrap.min.js') }}"></script>
<script src="{{ asset('crm/assets/scripts/datatables.demo.min.js') }}"></script>
<script src="{{ asset('crm/assets/plugin/lightview/js/lightview/lightview.js') }}"></script>
<script src="{{ asset('crm/assets/scripts/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('crm/assets/scripts/cells-by-row.min.js') }}"></script>

<script src="{{ asset('crm/assets/plugin/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('crm/assets/plugin/jquery-ui/jquery.ui.touch-punch.min.js') }}"></script>

<script>

$(document).ready(function () {
    $('.datepicker').datepicker({
        format: "dd-mm-yyyy",
        autoclose: true
    });

    $(".dropify").dropify();

    $('#tabsleft .finish').click(function() {
        $('#commentForm').submit();
    });
    $('#pincode_id').select2({
        placeholder: 'Enter Pincode',
        ajax: {
            url: '/getpincode',
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.pincode,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
    $('.select2_1').select2({

    });

    $(".allert").on("click",function(){
        var h_val = $(this).attr('data-href');
        swal({
			title: "Logout?",
			text: "Are you sure you want to logout?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor:"#f60e0e",
			confirmButtonText:"Yes, logout me!",
			cancelButtonText:"No, stay please!",
			closeOnConfirm: false,
			closeOnCancel: false
        },
        function(isConfirm) {
        if (isConfirm) {
            swal({
                title:"Logout success",
                text:"See you later!",
                type:"success",
                confirmButtonColor:"#304ffe"
            },
            function() {
                window.location.href = h_val;
            });
        } else {
            swal("Cancelled", "Your are safe :)", "error");
        }
        });
    });



    // $("#pass_allert").on("click",function(){
    //     // var h_val = $(this).attr('data-href');
    //     swal({
    //     title: "Change Password?",
    //     text: "",
    //     type: "type",
    //     showCancelButton: true,
    //     confirmButtonColor:"#f60e0e",
    //     confirmButtonText:"Yes, I'm out!",
    //     cancelButtonText:"No, stay plx!",
    //     closeOnConfirm: false,
    //     closeOnCancel: false
    //     },
    //     function(isConfirm) {
    //     if (isConfirm) {
    //         swal({
    //             title:"Logout success",
    //             text:"See you later!",
    //             type:"success",
    //             confirmButtonColor:"#304ffe"
    //         },
    //         function() {
    //             // window.location.href = h_val;
    //         });
    //     } else {
    //         swal("Cancelled", "Your are safe :)", "error");
    //     }
    //     });
    // });
    $('.menu ul li').find('a').each(function () {
            if (document.location.href == $(this).attr('href')) {
                $(this).parents().addClass("current active");
                $(this).addClass("active");
                // add class as you need ul or li or a
            }
        });
    $('.navigation ul li').find('a').each(function () {
            if (document.location.href == $(this).attr('href')) {
                $(this).parents().addClass("current active");
                $(this).addClass("active");
                // add class as you need ul or li or a
            }
        });
});




    </script>
    {{-- @livewireScripts --}}

	<script>
  $( function() {
    $( "#accordion" ).accordion();
  } );
  </script>

{{-- Permission Module Script --}}
<script>
    jQuery(document).ready(function () {
		// admin row script
        $('#admin-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#admin-all').prop('checked', false);
			} else {
				$('#admin-all').prop('checked', true);
			}
		});

		$('#admin-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.adminCheckbox').prop('checked', true);
			} else {
				$('.adminCheckbox').prop('checked', false);
			}
		});

		$('#admin-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#admin-read').prop('checked', true);
			}
		});

		// Advisor Script
		$('#advisor-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#advisor-all').prop('checked', false);
			} else {
				$('#advisor-all').prop('checked', true);
			}
		});

		$('#advisor-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.advisorCheckbox').prop('checked', true);
			} else {
				$('.advisorCheckbox').prop('checked', false);
			}
		});

		$('#advisor-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#advisor-read').prop('checked', true);
			}
		});

		// Loan script
		$('#loan-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#loan-all').prop('checked', false);
			} else {
				$('#loan-all').prop('checked', true);
			}
		});

		$('#loan-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.loanCheckbox').prop('checked', true);
			} else {
				$('.loanCheckbox').prop('checked', false);
			}
		});

		$('#loan-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#loan-read').prop('checked', true);
			}
		});

		// insurance script
		$('#insurance-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#insurance-all').prop('checked', false);
			} else {
				$('#insurance-all').prop('checked', true);
			}
		});

		$('#insurance-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.insuranceCheckbox').prop('checked', true);
			} else {
				$('.insuranceCheckbox').prop('checked', false);
			}
		});

		$('#insurance-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#insurance-read').prop('checked', true);
			}
		});

		// creditCard script
		$('#creditCard-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#creditCard-all').prop('checked', false);
			} else {
				$('#creditCard-all').prop('checked', true);
			}
		});

		$('#creditCard-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.creditCardCheckbox').prop('checked', true);
			} else {
				$('.creditCardCheckbox').prop('checked', false);
			}
		});

		$('#creditCard-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#creditCard-read').prop('checked', true);
			}
		});

		// socialCard script
		$('#socialCard-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#socialCard-all').prop('checked', false);
			} else {
				$('#socialCard-all').prop('checked', true);
			}
		});

		$('#socialCard-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.socialCardCheckbox').prop('checked', true);
			} else {
				$('.socialCardCheckbox').prop('checked', false);
			}
		});

		$('#socialCard-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#socialCard-read').prop('checked', true);
			}
		});

		// socialBanner script
		$('#socialBanner-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#socialBanner-all').prop('checked', false);
			} else {
				$('#socialBanner-all').prop('checked', true);
			}
		});

		$('#socialBanner-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.socialBannerCheckbox').prop('checked', true);
			} else {
				$('.socialBannerCheckbox').prop('checked', false);
			}
		});

		$('#socialBanner-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#socialBanner-read').prop('checked', true);
			}
		});


		// notification script
		$('#notification-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#notification-all').prop('checked', false);
			} else {
				$('#notification-all').prop('checked', true);
			}
		});

		$('#notification-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.notificationCheckbox').prop('checked', true);
			} else {
				$('.notificationCheckbox').prop('checked', false);
			}
		});

		$('#notification-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#notification-read').prop('checked', true);
			}
		});

		/* Payout Script */
		$('#payout-delete').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==false) {
				$('#payout-all').prop('checked', false);
			} else {
				$('#payout-all').prop('checked', true);
			}
		});

		$('#payout-all').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('.payoutCheckbox').prop('checked', true);
			} else {
				$('.payoutCheckbox').prop('checked', false);
			}
		});

		$('#payout-edit').change(function (e) { 
			e.preventDefault();
			if ($(this).is(":checked")==true) {
				$('#payout-read').prop('checked', true);
			}
		});

    });
</script>




</body>
</html>
