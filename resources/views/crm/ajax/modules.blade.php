<?php
$sessionArray = Session::get('sessionArray');
$user_type = $sessionArray['userDetails']['user_type'];
?>
<?php 
    if (isset($permissions['admin'])) {
        $adminPermission = explode(",",$permissions['admin']);
    } else {
        $adminPermission = [];
    }

    if (isset($permissions['advisor'])) {
        $advisorPermission = explode(",",$permissions['advisor']);
    } else {
        $advisorPermission = [];
    }

    if (isset($permissions['loan'])) {
        $loanPermission = explode(",",$permissions['loan']);
    } else {
        $loanPermission = [];
    }

    if (isset($permissions['insurance'])) {
        $insurancePermission = explode(",",$permissions['insurance']);
    } else {
        $insurancePermission = [];
    }

    if (isset($permissions['creditCard'])) {
        $creditCardPermission = explode(",",$permissions['creditCard']);
    } else {
        $creditCardPermission = [];
    }

    if (isset($permissions['socialCard'])) {
        $socialCardPermission = explode(",",$permissions['socialCard']);
    } else {
        $socialCardPermission = [];
    }

    if (isset($permissions['socialBanner'])) {
        $socialBannerPermission = explode(",",$permissions['socialBanner']);
    } else {
        $socialBannerPermission = [];
    }

    if (isset($permissions['notification'])) {
        $notificationPermission = explode(",",$permissions['notification']);
    } else {
        $notificationPermission =[];
    }

    if (isset($permissions['payout'])) {
        $payoutPermission = explode(",",$permissions['payout']);
    } else {
        $payoutPermission = [];
    }
?>
<div class="table-responsive">
    <table class="table table-striped table-hover table-condensed">
        <thead> 
            <tr> 
                <th>Module</th> 
                <th>All</th> 
                <th>Read/View</th> 
                <th>Add/Edit</th> 
                <th>Delete</th> 
            </tr> 
        </thead> 
        <tbody> 
			<?php if($user_type!="2") { ?>
            <tr> 
                <th scope="row">Admin Management</th>
                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-all" name="admin[1]" value="1" <?php if (in_array(1, $adminPermission)) { echo "checked"; } ?> /><label for="admin-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-read" name="admin[2]" value="2" <?php if (in_array(2, $adminPermission)) { echo "checked"; } ?> /><label for="admin-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-edit" name="admin[3]" value="3" <?php if (in_array(3, $adminPermission)) { echo "checked"; } ?> /><label for="admin-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-delete" name="admin[4]" value="4" <?php if (in_array(4, $adminPermission)) { echo "checked"; } ?> /><label for="admin-delete"></label></div></td> 
            </tr> 
			<?php } ?>
            <tr> 
                <th scope="row">Advisor Management</th>
                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-all" name="advisor[1]" value="1" <?php if (in_array(1, $advisorPermission)) { echo "checked"; } ?> /><label for="advisor-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-read" name="advisor[2]" value="2" <?php if (in_array(2, $advisorPermission)) { echo "checked"; } ?> /><label for="advisor-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-edit" name="advisor[3]" value="3" <?php if (in_array(3, $advisorPermission)) { echo "checked"; } ?> /><label for="advisor-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-delete" name="advisor[4]" value="4" <?php if (in_array(4, $advisorPermission)) { echo "checked"; } ?> /><label for="advisor-delete"></label></div></td> 
            </tr> 
            <tr> 
                <th scope="row">Loan</th>
                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-all" name="loan[1]" value="1" <?php if (in_array(1, $loanPermission)) { echo "checked"; } ?> /><label for="loan-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-read" name="loan[2]" value="2" <?php if (in_array(2, $loanPermission)) { echo "checked"; } ?> /><label for="loan-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-edit" name="loan[3]" value="3" <?php if (in_array(3, $loanPermission)) { echo "checked"; } ?> /><label for="loan-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-delete" name="loan[4]" value="4" <?php if (in_array(4, $loanPermission)) { echo "checked"; } ?> /><label for="loan-delete"></label></div></td> 
            </tr> 
            <tr> 
                <th scope="row">Insurance</th>
                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-all" name="insurance[1]" value="1" <?php if (in_array(1, $insurancePermission)) { echo "checked"; } ?> /><label for="insurance-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-read" name="insurance[2]" value="2" <?php if (in_array(2, $insurancePermission)) { echo "checked"; } ?> /><label for="insurance-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-edit" name="insurance[3]" value="3" <?php if (in_array(3, $insurancePermission)) { echo "checked"; } ?> /><label for="insurance-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-delete" name="insurance[4]" value="4" <?php if (in_array(4, $insurancePermission)) { echo "checked"; } ?> /><label for="insurance-delete"></label></div></td> 
            </tr> 
            <tr> 
                <th scope="row">Credit Card</th>
                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-all" name="creditCard[1]" value="1" <?php if (in_array(1, $creditCardPermission)) { echo "checked"; } ?> /><label for="creditCard-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-read" name="creditCard[2]" value="2" <?php if (in_array(2, $creditCardPermission)) { echo "checked"; } ?> /><label for="creditCard-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-edit" name="creditCard[3]" value="3" <?php if (in_array(3, $creditCardPermission)) { echo "checked"; } ?> /><label for="creditCard-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-delete" name="creditCard[4]" value="4" <?php if (in_array(4, $creditCardPermission)) { echo "checked"; } ?> /><label for="creditCard-delete"></label></div></td> 
            </tr> 
            <tr> 
                <th scope="row">Social Card</th>
                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-all" name="socialCard[1]" value="1" <?php if (in_array(1, $socialCardPermission)) { echo "checked"; } ?> /><label for="socialCard-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-read" name="socialCard[2]" value="2" <?php if (in_array(2, $socialCardPermission)) { echo "checked"; } ?> /><label for="socialCard-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-edit" name="socialCard[3]" value="3" <?php if (in_array(3, $socialCardPermission)) { echo "checked"; } ?> /><label for="socialCard-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-delete" name="socialCard[4]" value="4" <?php if (in_array(4, $socialCardPermission)) { echo "checked"; } ?> /><label for="socialCard-delete"></label></div></td> 
            </tr> 
            <tr> 
                <th scope="row">Social Banners</th>
                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-all" name="socialBanner[1]" value="1" <?php if (in_array(1, $socialBannerPermission)) { echo "checked"; } ?> /><label for="socialBanner-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-read" name="socialBanner[2]" value="2" <?php if (in_array(2, $socialBannerPermission)) { echo "checked"; } ?> /><label for="socialBanner-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-edit" name="socialBanner[3]" value="3" <?php if (in_array(3, $socialBannerPermission)) { echo "checked"; } ?> /><label for="socialBanner-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-delete" name="socialBanner[4]" value="4" <?php if (in_array(4, $socialBannerPermission)) { echo "checked"; } ?> /><label for="socialBanner-delete"></label></div></td> 
            </tr>
            <tr> 
                <th scope="row">Notifications</th>
                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-all" name="notification[1]" value="1" <?php if (in_array(1, $notificationPermission)) { echo "checked"; } ?> /><label for="notification-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-read" name="notification[2]" value="2" <?php if (in_array(2, $notificationPermission)) { echo "checked"; } ?> /><label for="notification-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-edit" name="notification[3]" value="3" <?php if (in_array(3, $notificationPermission)) { echo "checked"; } ?> /><label for="notification-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-delete" name="notification[4]" value="4" <?php if (in_array(4, $notificationPermission)) { echo "checked"; } ?> /><label for="notification-delete"></label></div></td> 
            </tr>
            <tr> 
                <th scope="row">Payouts</th>
                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-all" name="payout[1]" value="1" <?php if (in_array(1, $payoutPermission)) { echo "checked"; } ?> /><label for="payout-all"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-read" name="payout[2]" value="2" <?php if (in_array(2, $payoutPermission)) { echo "checked"; } ?> /><label for="payout-read"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-edit" name="payout[3]" value="3" <?php if (in_array(3, $payoutPermission)) { echo "checked"; } ?> /><label for="payout-edit"></label></div></td> 
                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-delete" name="payout[4]" value="4" <?php if (in_array(4, $payoutPermission)) { echo "checked"; } ?> /><label for="payout-delete"></label></div></td> 
            </tr> 
            
        </tbody> 
    </table> 
</div>

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