@extends('crm.crmlayout')
@section('content')

<div class="row">
<div class="col-md-12 col-xs-12 col-lg-12">
    @if(session('status'))
        <div class="alert alert-success mb-1 mt-1">
        {{ session('status') }}
        </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success" style="border: 2px solid #8AAC8A;">
            <strong>{{ $message }}</strong>
        </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger" style="border: 2px solid #CE9694;">
        <span style="margin-left: 25px;"><strong>Please fix the below errors!<hr></strong>
        </span>        
        <ol>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ol>
    </div>
    @endif

    <div class="box-content">
        <h4 class="box-title">Manage Permissions</h4><hr>

        <form id="permissionForm" action="{{ route('crm.store-permission') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            
            <div class="form-group margin-bottom-20">
                <label for="insurance"><h5 class="text-justify">User Types</h5></label>
                <select class="form-control" name="userType" id="userType">
                    <?php foreach ($user_type as $id => $userType) { ?>
                        <option value="<?= $id; ?>"><?= ucfirst($userType); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="box-content" id="js-modules">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
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
                            <?php //foreach ($crm_modules as $key => $value) { ?>
                            {{-- <tr> 
                                <th scope="row"><?= ucfirst($value); ?></th>
                                <td><div class="switch success"><input type="checkbox" checked class="<?= $value; ?>Checkbox" id="<?= $value; ?>-all" name="<?= $value; ?>[1]" value="1" ><label for="<?= $value; ?>-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" checked class="<?= $value; ?>Checkbox" id="<?= $value; ?>-read" name="<?= $value; ?>[2]" value="2" ><label for="<?= $value; ?>-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" checked class="<?= $value; ?>Checkbox" id="<?= $value; ?>-edit" name="<?= $value; ?>[3]" value="3" ><label for="<?= $value; ?>-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" checked class="<?= $value; ?>Checkbox" id="<?= $value; ?>-delete" name="<?= $value; ?>[4]" value="4" ><label for="<?= $value; ?>-delete"></label></div></td> 
                            </tr> --}}
                            <?php //} ?>

                            
                            <tr> 
                                <th scope="row">Admin Management</th>
                                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-all" name="admin[1]" value="1" ><label for="admin-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-read" name="admin[2]" value="2" ><label for="admin-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-edit" name="admin[3]" value="3" ><label for="admin-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="adminCheckbox" id="admin-delete" name="admin[4]" value="4" ><label for="admin-delete"></label></div></td> 
                            </tr> 
                            <tr> 
                                <th scope="row">Advisor Management</th>
                                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-all" name="advisor[1]" value="1" /><label for="advisor-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-read" name="advisor[2]" value="2" ><label for="advisor-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-edit" name="advisor[3]" value="3" ><label for="advisor-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="advisorCheckbox" id="advisor-delete" name="advisor[4]" value="4" ><label for="advisor-delete"></label></div></td> 
                            </tr> 
                            <tr> 
                                <th scope="row">Loan</th>
                                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-all" name="loan[1]" value="1" ><label for="loan-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-read" name="loan[2]" value="2" ><label for="loan-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-edit" name="loan[3]" value="3" ><label for="loan-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="loanCheckbox" id="loan-delete" name="loan[4]" value="4" ><label for="loan-delete"></label></div></td> 
                            </tr> 
                            <tr> 
                                <th scope="row">Insurance</th>
                                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-all" name="insurance[1]" value="1" ><label for="insurance-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-read" name="insurance[2]" value="2"><label for="insurance-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-edit" name="insurance[3]" value="3" ><label for="insurance-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="insuranceCheckbox" id="insurance-delete" name="insurance[4]" value="4" ><label for="insurance-delete"></label></div></td> 
                            </tr> 
                            <tr> 
                                <th scope="row">Credit Card</th>
                                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-all" name="creditCard[1]" value="1" ><label for="creditCard-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-read" name="creditCard[2]" value="2" ><label for="creditCard-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-edit" name="creditCard[3]" value="3" ><label for="creditCard-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="creditCardCheckbox" id="creditCard-delete" name="creditCard[4]" value="4" ><label for="creditCard-delete"></label></div></td> 
                            </tr> 
                            <tr> 
                                <th scope="row">Social Card</th>
                                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-all" name="socialCard[1]" value="1" ><label for="socialCard-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-read" name="socialCard[2]" value="2" ><label for="socialCard-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-edit" name="socialCard[3]" value="3" ><label for="socialCard-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="socialCardCheckbox" id="socialCard-delete" name="socialCard[4]" value="4" ><label for="socialCard-delete"></label></div></td> 
                            </tr> 
                            <tr> 
                                <th scope="row">Social Banners</th>
                                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-all" name="socialBanner[1]" value="1" ><label for="socialBanner-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-read" name="socialBanner[2]" value="2" ><label for="socialBanner-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-edit" name="socialBanner[3]" value="3" ><label for="socialBanner-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="socialBannerCheckbox" id="socialBanner-delete" name="socialBanner[4]" value="4" ><label for="socialBanner-delete"></label></div></td> 
                            </tr>
                            <tr> 
                                <th scope="row">Notifications</th>
                                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-all" name="notification[1]" value="1" ><label for="notification-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-read" name="notification[2]" value="2" ><label for="notification-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-edit" name="notification[3]" value="3" ><label for="notification-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="notificationCheckbox" id="notification-delete" name="notification[4]" value="4" ><label for="notification-delete"></label></div></td> 
                            </tr>
                            <tr> 
                                <th scope="row">Payouts</th>
                                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-all" name="payout[1]" value="1" ><label for="payout-all"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-read" name="payout[2]" value="2" ><label for="payout-read"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-edit" name="payout[3]" value="3" ><label for="payout-edit"></label></div></td> 
                                <td><div class="switch success"><input type="checkbox" class="payoutCheckbox" id="payout-delete" name="payout[4]" value="4" ><label for="payout-delete"></label></div></td> 
                            </tr> 
                            
                        </tbody> 
                    </table> 
                </div>
            </div>
			
            <hr>
            <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Updates   </button>
        </form>
    </div>

</div>

</div>

<script>
    $(document).ready(function () {
        var userType = $('#userType').val();
        loadModules(userType);
        $('#userType').on('change', function() {
            var userType = $(this).val();
            loadModules(userType);
        });        
    });

    function loadModules(userType) {
        $.ajax({
            url:"{{ url('load-modules') }}",
            type: "POST",
            data: {
                userType: userType,
            _token: '{{csrf_token()}}'
            },
            dataType : 'html',
            success: function(result){
                // console.log(result);
                $('#js-modules').html(result);
            }
        });
    }

</script>


@endsection
