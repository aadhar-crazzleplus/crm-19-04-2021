@extends('crm.crmlayout')

@section('content')

<div class="row small-spacing">
    <div class="col-xs-12">
        <div class="box-content">
            <h4 class="box-title">Personal Loan Listing</h4>
            <!-- /.box-title -->
            <div class="dropdown js__drop_down">
                <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
                <ul class="sub-menu">
                    <li><a href="{{route('pl-view')}}">All</a></li>
                    {{-- <li><a data-toggle="modal" href="{{route('veh-add-loan')}}">Add News</a></li> --}}
                    <li><a data-toggle="modal" data-target="#myModal1" href="#myModal1">Add News</a></li>
                    <li><a href="{{route('pl-deleted')}}">Deleted List</a></li>
                    <li class="split"></li>
                </ul>
                <!-- /.sub-menu -->
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <!-- /.dropdown js__dropdown -->
            <table id="usrTable" class="table table-striped table-bordered display" style="width:100%">
                <thead>
                    <tr>
                        <th>Lead Name</th>
                        <th>Mobile No</th>
                        <th>Monthly Salary</th>
                        <th>Company Vintage</th>
                        <th>Assign To</th>
                        <th>Status</th>
                        <th>Updated at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Lead Name</th>
                        <th>Mobile No</th>
                        <th>Monthly Salary</th>
                        <th>Company Vintage</th>
                        <th>Assign To</th>
                        <th>Status</th>
                        <th>Updated at</th>
                        <th>Action</th>
                    </tr>
                </tfoot>

            </table>
        </div>
        <!-- /.box-content -->
    </div>
    <!-- /.col-xs-12 -->

</div>


<script>

$(document).ready(function () {

// DataTable
var table = $('#usrTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('getplleads')}}",
        columns: [
        { data: 'full_name' },
        { data: 'mobile_no' },
        { data: 'monthly_salary' },
        { data: 'company_vintage' },
        { data: 'assign_to' },
        { data: 'lead_status'},
        { data: 'updated_at'},
        { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[ 6, "desc" ]],
        dom: 'lBfrtip',
        buttons: [
            { extend: 'colvis', columns: ':not(:first-child)' },
            { text: 'Add New', action: function ( e, dt, node, config ) { $('#myModal1').modal('show'); } },
        ],
    });

    $(".vhclose").on("click", function(){
        table.ajax.reload();
        $('#form1')[0].reset();
        $('#form2')[0].reset();
        $('#form3')[0].reset();
        $('#form4')[0].reset();
    });
    $(".edit_vhclose").on("click", function(){
        table.ajax.reload();

        $('#edit_form2')[0].reset();
        $('#edit_form3')[0].reset();
        $('#edit_form4')[0].reset();
    });

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "rtl": false,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": 300,
        "hideDuration": 1000,
        "timeOut": 10000,
        "extendedTimeOut": 1000,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    $("div[id^='myModal']").each(function(){

        var currentModal = $(this);
        $(".processing").hide();

        //click next
        currentModal.find('.btn-next').click(function(){
            $(".regu").hide();
            $(".processing").show();
            var froms = $(this).data("form");
            var formId = $('#'+froms)[0];
            var fd = new FormData(formId);

            $.ajax({
                url: $(this).data("route"),
                processData: false,
                contentType: false,
                method: 'post',
                // data: {
                //     mobile_no: $('#mobile_no').val(),
                //     regn_no: $('#regn_no').val(),
                //     _token: '{{csrf_token()}}'
                // },
                data: fd,
                success: function(result) {
                    $(".regu").show();
                    $(".processing").hide();
                    // console.log('Error:', result);
                    if(result.success) {
                        toastr["success"]("Performed !");
                        currentModal.modal('hide');
                        currentModal.closest("div[id^='myModal']").nextAll("div[id^='myModal']").first().modal('show');
                        if(froms == "form1"){
                            getpincodebyid(result.data.address.pincode_id, 'add2_pincodes_id');
                            $("#add2_profile_id").val(result.data.profile.profile_id);
                            $("#add2_address_id").val(result.data.address.address_id);
                            $("#add2_full_name").val(result.data.profile.full_name);
                            $("#add2_mobile_no").val(result.data.profile.mobile_no);
                            $("#add2_email").val(result.data.profile.email);
                            $("#add2_dob").val(result.data.profile.dob);
                            $("#add2_address").val(result.data.address.address);
                        }
                        if(froms == "form2"){
                            $("#add3_profile_id").val(result.data.profile_id);
                            $("#add3_lead_id").val(result.data.lead_id);
                        }
                        if(froms == "form3"){
                            getpincodebyid(result.data.income.pincode_id, 'add4_pincodes_id');
                            getcompanybyid(result.data.income.company_id, 'add4_company_id');
                            $("#add4_profile_id").val(result.data.profile_id);
                            $("#add4_lead_id").val(result.data.lead_id);
                            $("#add4_monthly_salary").val(result.data.income.monthly_salary);
                            $("#add4_designation").val(result.data.income.designation);
                            $("#add4_company_vintage").val(result.data.income.company_vintage).trigger('change');
                            $("#add4_office_email").val(result.data.income.office_email);
                            $("#add4_company_address").val(result.data.income.company_address);
                        }
                        if(froms == "form4"){
                            table.ajax.reload();
                            $('#form1')[0].reset();
                            $('#form2')[0].reset();
                            $('#form3')[0].reset();
                            $('#form4')[0].reset();
                        }

                    } else {
                        var err = '';
                        $.each(result.data, function(key, value) {
                            err += value+"<br>";
                            $('#'+key).addClass('has-error');
                        });
                        toastr["error"](err);
                    }
                },
                error: function (data) {
                    $(".regu").show();
                    $(".processing").hide();
                    var err = '';
                    $.each(data.responseJSON.data, function(key, value) {
                        err += value+"<br>";
                    });
                    toastr["error"](err);
                }
            });
        });

        //click prev
        currentModal.find('.btn-prev').click(function(){
            currentModal.modal('hide');
            currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().modal('show');
        });

    });

    $("div[id^='editModal']").each(function(){

    var currentEditModal = $(this);
    $(".processing").hide();


    //click next
    currentEditModal.find('.btn-next').click(function(){
        $(".regu").hide();
        $(".processing").show();
        var froms = $(this).data("form");
        var formId = $('#'+froms)[0];
        var fd = new FormData(formId);

        $.ajax({
            url: $(this).data("route"),
            processData: false,
            contentType: false,
            method: 'post',
            // data: {
            //     mobile_no: $('#mobile_no').val(),
            //     regn_no: $('#regn_no').val(),
            //     _token: '{{csrf_token()}}'
            // },
            data: fd,
            success: function(result) {
                $(".regu").show();
                $(".processing").hide();
                // console.log('Error:', result);
                if(result.success) {
                    toastr["success"]("Performed !");
                    currentEditModal.modal('hide');
                    currentEditModal.closest("div[id^='editModal']").nextAll("div[id^='editModal']").first().modal('show');

                    if(froms == "edit_form2"){

                        $("#edit3_profile_id").val(result.data.profile_id);
                        $("#edit3_lead_id").val(result.data.lead_id);
                        $("#edit3_loan_id").val(result.data.is_loan.loan_id);
                        $("#edit3_total_rem_loan").val(result.data.is_loan.total_rem_loan);
                        $("#edit3_monthly_emi").val(result.data.is_loan.monthly_emi);
                        $("#edit3_lead_remark").val(result.data.lead_remark);
                        $("#edit3_lead_status").val(result.data.lead_status);

                        // $('#regn_dt').val(result.data.regn_dt).trigger('change');
                    }
                    if(froms == "edit_form3"){
                        getpincodebyid(result.data.income.pincode_id, 'edit4_pincodes_id');
                        getcompanybyid(result.data.income.company_id, 'edit4_company_id');
                        $("#edit4_profile_id").val(result.data.profile_id);
                        $("#edit4_lead_id").val(result.data.lead_id);
                        $("#edit4_monthly_salary").val(result.data.income.monthly_salary);
                        $("#edit4_designation").val(result.data.income.designation);
                        $("#edit4_company_vintage").val(result.data.income.company_vintage).trigger('change');
                        $("#edit4_office_email").val(result.data.income.office_email);
                        $("#edit4_company_address").val(result.data.income.company_address);
                        $("#edit4_lead_remark").val(result.data.lead_remark);
                        $("#edit4_lead_status").val(result.data.lead_status).trigger('change');
                    }
                    if(froms == "edit_form4"){
                        table.ajax.reload();
                        $('#edit_form2')[0].reset();
                        $('#edit_form3')[0].reset();
                        $('#edit_form4')[0].reset();
                    }

                } else {
                    var err = '';
                    $.each(result.data, function(key, value) {
                        err += value+"<br>";
                        $('#'+key).addClass('has-error');
                    });
                    toastr["error"](err);
                }
            },
            error: function (data) {
                $(".regu").show();
                $(".processing").hide();
                var err = '';
                $.each(data.responseJSON.data, function(key, value) {
                    err += value+"<br>";
                });
                toastr["error"](err);
            }
        });
    });

        //click prev
        currentEditModal.find('.btn-prev').click(function(){
            currentEditModal.modal('hide');
            currentEditModal.closest("div[id^='editModal']").prevAll("div[id^='editModal']").first().modal('show');
        });

    });


    $('body').on('shown.bs.modal', '.modal', function() {
    $(this).find('select').each(function() {
        var dropdownParent = $(document.body);
        if ($(this).parents('.modal.in:first').length !== 0)
        dropdownParent = $(this).parents('.modal.in:first');
        $(".lead_by").select2({
        dropdownParent: dropdownParent,
        placeholder: 'Enter Advisor Name',
            ajax: {
                url: '/getadvisor',
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.first_name+" "+item.last_name+" - "+item.user_code,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
        $(".assign_to").select2({
        dropdownParent: dropdownParent,
        placeholder: 'Enter Advisor Name',
            ajax: {
                url: '/getadvisor',
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.first_name+" "+item.last_name+" - "+item.user_code,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
        $(".select2_1").select2({
            dropdownParent: dropdownParent
        });
        $('.pincodes_id').select2({
            dropdownParent: dropdownParent,
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
        $('.company_search').select2({
            dropdownParent: dropdownParent,
            placeholder: 'Enter Company Name',
            ajax: {
                url: '/search_companies',
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.company_name,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

    });
    });


    $('.state_id').on('change', function() {
        var state_id = this.value;
        var obj = this;
        $(".city_id").html('');
        $.ajax({
            url:"{{url('getcity')}}",
            type: "POST",
            data: {
            state_id: state_id,
            _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result){
                $('.city_id').html('<option>Select City</option>');
                $.each(result,function(key,value){
                    $(".city_id").append('<option value="'+value.id+'">'+value.city_name+'</option>');
                });
                var temps = $('#temps').val();
                $('.city_id').val(temps).trigger('change');
            }
        });
    });

    $('.pincodes_id').on('change', function() {
        var pincode_id = this.value;
        $(".city_id").html('');
        // $("#state_id").html('');
        $.ajax({
            url:"{{url('getcitystate')}}",
            type: "POST",
            data: {
                pincode_id: pincode_id,
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result){
                $('#temps').val(result.citystate_id.city_id);
                $('.state_id').val(result.citystate_id.state_id).trigger('change');

            }
        });
    });

});

function getpincodebyid(pincodesId, obj){
    var id = obj;
    $.ajax({
        url:"{{url('getpincodebyid')}}",
        type: "POST",
        data: {
            pincode_id: pincodesId,
            _token: '{{csrf_token()}}'
        },
        dataType : 'json',
        success: function(result){
            // $('#pincodes_id').html('<option>Select City</option>');
            // $.each(result,function(key,value){
                $("#"+id).append('<option value="'+result.id+'">'+result.pincode+'</option>');
            // });
            // var temps = $('#temps').val();
            $('#'+id).val(result.id).trigger('change');
        }
    });
}

function getcompanybyid(companyId, obj){
    var id = obj;
    $.ajax({
        url:"{{url('company_list')}}",
        type: "POST",
        data: {
            company_id: companyId,
            _token: '{{csrf_token()}}'
        },
        dataType : 'json',
        success: function(result){
            // $('#pincodes_id').html('<option>Select City</option>');
            // $.each(result,function(key,value){
                $("#"+id).append('<option value="'+result.id+'">'+result.company_name+'</option>');
            // });
            // var temps = $('#temps').val();
            $('#'+id).val(result.id).trigger('change');
        }
    });
}

function editLead(edit_route){
    $.ajax({
        url:edit_route,
        type: "GET",
        // data: {
        //     _token: '{{csrf_token()}}'
        // },
        dataType : 'json',
        success: function(result){
            getpincodebyid(result.data.address.pincode_id, 'edit2_pincodes_id');
            $("#edit2_lead_by").append('<option value="'+result.data.lead_by_id+'">'+result.data.lead_by+'</option>');
            $('#edit2_lead_by').val(result.data.lead_by_id).trigger('change');

            $("#edit2_assign_to").append('<option value="'+result.data.assign_to_id+'">'+result.data.assign_to+'</option>');
            $('#edit2_assign_to').val(result.data.assign_to_id).trigger('change');

            $("#edit2_profile_id").val(result.data.profile.profile_id);
            $("#edit2_address_id").val(result.data.address.address_id);
            $("#edit2_full_name").val(result.data.profile.full_name);
            $("#edit2_mobile_no").val(result.data.profile.mobile_no);
            $("#edit2_email").val(result.data.profile.email);
            $("#edit2_dob").val(result.data.profile.dob);
            $("#edit2_address").val(result.data.address.address);
        }
    });
}

function showLead(id){
    $.ajax({
        url:"{{route('pl-show')}}",
        type: "POST",
        data: {
            id:id,
            _token: '{{csrf_token()}}'
        },
        dataType : 'json',
        success: function(result){
            $("#show_product_title").html(result.data.product_title);
            $("#show_lead_id").html(result.data.lead_id);
            $("#show_lead_status").html(result.data.lead_status);
            $("#show_lead_remark").html(result.data.lead_remark);
            $("#show_lead_by").html(result.data.lead_by);
            $("#show_assign_to").html(result.data.assign_to);
            $("#show_updated_by").html(result.data.updated_by);

            $("#show_full_name").html(result.data.full_name);
            $("#show_mobile_no").html(result.data.mobile_no);
            $("#show_email").html(result.data.email);
            $("#show_dob").html(result.data.dob);
            $("#show_address").html(result.data.address);
            $("#show_pincode").html(result.data.pincode);
            $("#show_city").html(result.data.city);
            $("#show_state").html(result.data.state);

            $("#show_total_rem_loan").html(result.data.total_rem_loan);
            $("#show_monthly_emi").html(result.data.monthly_emi);

            $("#show_company_name").html(result.data.company_name);
            $("#show_monthly_salary").html(result.data.monthly_salary);
            $("#show_company_vintage").html(result.data.company_vintage);
            $("#show_designation").html(result.data.designation);
            $("#show_office_email").html(result.data.office_email);
            $("#show_company_address").html(result.data.company_address);

        }
    });
}

function deleete(val){
        swal({
            title: "Delete?",
            text: "Are you sure you want to Delete?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor:"#f60e0e",
            confirmButtonText:"Yes, I'm!",
            cancelButtonText:"No, Pressed Mistake!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
        if (isConfirm) {
            swal({
                title:"User deleted successfully",
                text:"See you later!",
                type:"success",
                confirmButtonColor:"#304ffe"
            },
            function() {
                window.location.href = val;
            });
        } else {
            swal("Cancelled", "All right !! User is still safe :)", "error");
        }
        });
    }
</script>
@endsection

@section('addmodals')
{{-- Modals --}}
<!-- Button trigger add modal -->
    <!-- add Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Besic Info</h4>
              </div>
              <div class="modal-body">
                 <form role="form" id="form1" action="" method="post" class="registration-form">
                    <fieldset style="display: block;">
                       <div class="form-top">
                          <div class="form-top-left">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%">
                                    <span class="sr-onlys">25% Completing</span>
                                </div>
                            </div>

                          </div>

                       </div>
                       @csrf
                       <div class="form-bottom">
                          <div class="form-group">
                             {{-- <label class="sr-only" for="mobile_no">Mobile Number</label> --}}
                             <label for="mobile_no">Mobile Number</label>
                             <div class="controls">
                                <input name="mobile_no" placeholder="Mobile number..." class="form-control input-error" id="mobile_no" type="number" max="10" maxlength="10">
                             </div>
                          </div>

                          {{-- <button type="button" class="btn btn-next">Next</button> --}}
                       </div>
                    </fieldset>

                 </form>
              </div>
              <div class="modal-footer">
                 {{-- <button type="button" class="btn btn-default regu btn-prev">Prev</button> --}}
                 <button type="button" id="next1" data-form="form1" data-route="{{ route('pl-basic-add') }}" class="btn btn-default regu btn-next">Next</button>
                 <button type="button" class="btn btn-default regu vhclose" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- Add Modal -->
     <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog  modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:560px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Personal Details</h4>
              </div>
              <div class="modal-body">
                <form id="form2" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="add2_profile_id" value="" />
                    <input type="hidden" name="address_id" id="add2_address_id" value="" />
                    <input type="hidden" name="temp" id="temps" >
                 <fieldset>
                       <div class="form-top">
                          <div class="form-top-left">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">
                                    <span class="sr-onlys">50% Completing</span>
                                </div>
                            </div>
                          </div>

                       </div>
                       <div class="form-bottom">
                        <div class="form-group">
                            <label for="lead_by">Lead By</label>
                            <div class="controls">
                                <select class="required lead_by livesearch form-control" name="lead_by" id="add2_lead_by">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="assign_to">Assign To</label>
                            <div class="controls">
                                <select class="required assign_to livesearch form-control" name="assign_to" id="add2_assign_to">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="full_name">Customer Name</label>
                            <div class="controls">
                               <input name="full_name" placeholder="Customer Name..." class="form-control" id="add2_full_name" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="mobile_no">Customer Mobile Number</label>
                            <div class="controls">
                               <input name="mobile_no" placeholder="Customer Mobile No..." class="form-control" id="add2_mobile_no" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="email">Customer Email</label>
                            <div class="controls">
                               <input name="email" placeholder="Customer email..." class="form-control" id="add2_email" type="email">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="dob">Customer DOB</label>
                            <div class="controls">
                                <input name="dob" class="form-control datepicker" placeholder="dd-mm-yyyy" id="add2_dob" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="address">Customer Address</label>
                            <div class="controls">
                               <input name="address" placeholder="Owner address..." class="form-control" id="add2_address" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="pincodes_id">Pincode</label>
                            <div class="controls">
                                <select class="required pincodes_id livesearch form-control" name="pincode_id" id="add2_pincodes_id">
                                    <option value="">Pincodes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                <select class=" form-control state_id select2_1" name="state_id" id="add2_state_id">
                                        @foreach ($states as $state)
                                        <option value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class=" form-control city_id select2_1" name="city_id" id="add2_city_id">
                                    <option>Select City</option>
                            </select>
                            </div>
                        </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="pre1" class="btn btn-default regu btn-prev">Prev</button>
                 <button type="button" id="next2" data-form="form2" data-route="{{ route('pl-profile-add') }}" class="btn btn-default regu btn-next">Next</button>
                 <button type="button" class="btn btn-default regu vhclose" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- add Modal -->
     <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:560px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Loan Details</h4>
              </div>
              <div class="modal-body">
                <form id="form3" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="add3_profile_id" value="" />
                    <input type="hidden" name="lead_id" id="add3_lead_id" value="" />

                 <fieldset>
                       <div class="form-top">
                          <div class="form-top-left">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">
                                    <span class="sr-onlys">75% Completing</span>
                                </div>
                            </div>
                          </div>

                       </div>
                       <div class="form-bottom">
                        <div class="form-group">
                            <label for="total_rem_loan">Total Remaining Loan Amount</label>
                            <div class="controls">
                               <input name="total_rem_loan" placeholder="Total Remaining Loan Amount..." class="form-control" id="add3_total_rem_loan" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="monthly_emi">Monthly EMI</label>
                            <div class="controls">
                               <input name="monthly_emi" placeholder="Owner Mobile No..." class="form-control" id="add3_monthly_emi" type="text">
                            </div>
                         </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="pre2" class="btn btn-default regu btn-prev">Prev</button>
                 <button type="button" id="next3" data-form="form3" data-route="{{ route('pl-is-loan-add') }}" class="btn btn-default regu btn-next">Next</button>
                 <button type="button" class="btn btn-default regu" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal 4 -->

     <!-- add 4 Modal -->
     <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:560px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Employment Details</h4>
              </div>
              <div class="modal-body">
                <form id="form4" action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="profile_id" id="add4_profile_id" value="" />
                    <input type="hidden" name="lead_id" id="add4_lead_id" value="" />
                    <input type="hidden" name="company_name" id="company_name" value="" />
                 <fieldset>
                       <div class="form-top">
                          <div class="form-top-left">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-onlys">100% Completing</span>
                                </div>
                            </div>
                          </div>

                       </div>
                       <div class="form-bottom">
                        <div class="form-group">
                            <label for="company_id">Comapny Name</label>
                            <div class="controls">
                                <select class="required company_search livesearch form-control" name="company_id" id="add4_company_id">
                                    <option value="">Company Name</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group nonsalary">
                            <label for="designation">Designation</label>
                            <div class="controls">
                               <input name="designation" placeholder="designation..." class="form-control" id="add4_designation" type="text">
                            </div>
                         </div>
                         <div class="form-group salary">
                            <label for="company_vintage">Time in current company</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="company_vintage" id="add4_company_vintage">
                                    <option value="Less Then 1 Year">Less Then 1 Year</option>
                                    <option value="1 Year">1 Year</option>
                                    <option value="2 Years">2 Years</option>
                                    <option value="3 Years">3 Years</option>
                                    <option value="More Then 3 Years">More Then 3 Years</option>
                                </select>
                            </div>
                         </div>
                         <div class="form-group salary">
                            <label for="monthly_salary">Net monthly salary(In hand)</label>
                            <div class="controls">
                               <input name="monthly_salary" placeholder="Net monthly salary..." class="form-control" id="add4_monthly_salary" type="text">
                            </div>
                         </div>
                         <div class="form-group nonsalary">
                            <label for="office_email">Office Email</label>
                            <div class="controls">
                               <input name="office_email" placeholder="Office Email..." class="form-control" id="add4_office_email" type="text">
                            </div>
                         </div>
                         <div class="form-group nonsalary">
                            <label for="company_address">Company Address</label>
                            <div class="controls">
                               <input name="company_address" placeholder="Company Address..." class="form-control" id="add4_company_address" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="pincodes_id">Pincode</label>
                            <div class="controls">
                                <select class="required pincodes_id livesearch form-control" name="pincode_id" id="add4_pincodes_id">
                                    <option value="">Pincodes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                <select class=" form-control state_id select2_1" name="state_id" id="add4_state_id">
                                        @foreach ($states as $state)
                                        <option value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class=" form-control city_id select2_1" name="city_id" id="add4_city_id">
                                    <option>Select City</option>
                            </select>
                            </div>
                        </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="pre3" class="btn btn-default regu btn-prev">Prev</button>
                 <button type="button" id="next4" data-form="form4" data-route="{{ route('pl-income-add') }}" class="btn btn-default regu btn-next">Finish</button>
                 {{-- <button type="button" class="btn btn-default regu" data-dismiss="modal">Close</button> --}}
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal 5-->

     <!-- Edit Modal -->
     <div class="modal fade" id="editModal2" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog  modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:560px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close edit_vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="editModalLabel">Profile Details</h4>
              </div>
              <div class="modal-body">
                <form id="edit_form2" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="edit2_profile_id" value="" />
                    <input type="hidden" name="address_id" id="edit2_address_id" value="" />
                    <input type="hidden" name="temp" id="edit2_temps" >
                 <fieldset>
                       <div class="form-top">
                          <div class="form-top-left">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%">
                                    <span class="sr-onlys">33% Completing</span>
                                </div>
                            </div>
                          </div>

                       </div>
                       <div class="form-bottom">
                        <div class="form-group">
                            <label for="lead_by">Lead By</label>
                            <div class="controls">
                                <select class="required livesearch form-control" name="lead_by" id="edit2_lead_by">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="assign_to">Assign To</label>
                            <div class="controls">
                                <select class="required livesearch form-control" name="assign_to" id="edit2_assign_to">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="full_name">Customer Name</label>
                            <div class="controls">
                               <input name="full_name" placeholder="Customer Name..." class="form-control" id="edit2_full_name" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="mobile_no">Customer Mobile Number</label>
                            <div class="controls">
                               <input name="mobile_no" placeholder="Customer Mobile No..." class="form-control" id="edit2_mobile_no" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="email">Customer Email</label>
                            <div class="controls">
                               <input name="email" placeholder="Customer email..." class="form-control" id="edit2_email" type="email">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="dob">Customer DOB</label>
                            <div class="controls">
                                <input name="dob" class="form-control datepicker" placeholder="dd-mm-yyyy" id="edit2_dob" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="address">Customer Address</label>
                            <div class="controls">
                               <input name="address" placeholder="Owner address..." class="form-control" id="edit2_address" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="pincodes_id">Pincode</label>
                            <div class="controls">
                                <select class="required pincodes_id livesearch form-control" name="pincode_id" id="edit2_pincodes_id">
                                    <option value="">Pincodes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                <select class=" form-control state_id select2_1" name="state_id" id="edit2_state_id">
                                        @foreach ($states as $state)
                                        <option value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class=" form-control city_id select2_1" name="city_id" id="edit2_city_id">
                                    <option>Select City</option>
                            </select>
                            </div>
                        </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="edit_pre1" class="btn btn-default regu btn-prev">Prev</button>
                 <button type="button" id="edit_next2" data-form="edit_form2" data-route="{{ route('pl-profile-update') }}" class="btn btn-default regu btn-next">Next</button>
                 <button type="button" class="btn btn-default regu edit_vhclose" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- Edit Modal -->
     <div class="modal fade" id="editModal3" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:560px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close edit_vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="editModalLabel">Loan Details</h4>
              </div>
              <div class="modal-body">
                <form id="edit_form3" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="edit3_profile_id" value="" />
                    <input type="hidden" name="lead_id" id="edit3_lead_id" value="" />
                    <input type="hidden" name="loan_id" id="edit3_loan_id" value="" />
                    <input type="hidden" name="lead_status" id="edit3_lead_status" value="" />
                    <input type="hidden" name="lead_remark" id="edit3_lead_remark" value="" />

                 <fieldset>
                       <div class="form-top">
                          <div class="form-top-left">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100" style="width: 66%">
                                    <span class="sr-onlys">66% Completing</span>
                                </div>
                            </div>
                          </div>

                       </div>
                       <div class="form-bottom">
                        <div class="form-group">
                            <label for="total_rem_loan">Total Remaining Loan Amount</label>
                            <div class="controls">
                               <input name="total_rem_loan" placeholder="Total Remaining Loan Amount..." class="form-control" id="edit3_total_rem_loan" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="monthly_emi">Monthly EMI</label>
                            <div class="controls">
                               <input name="monthly_emi" placeholder="Owner Mobile No..." class="form-control" id="edit3_monthly_emi" type="text">
                            </div>
                         </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="edit_pre2" class="btn btn-default regu btn-prev">Prev</button>
                 <button type="button" id="edit_next3" data-form="edit_form3" data-route="{{ route('pl-is-loan-update') }}" class="btn btn-default regu btn-next">Next</button>
                 <button type="button" class="btn btn-default regu edit_vhclose" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>


     <!-- edi 4 Modal -->
     <div class="modal fade" id="editModal4" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:560px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close edit_vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="editModalLabel">Employment Details</h4>
              </div>
              <div class="modal-body">
                <form id="edit_form4" action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="profile_id" id="edit4_profile_id" value="" />
                    <input type="hidden" name="lead_id" id="edit4_lead_id" value="" />
                 <fieldset>
                       <div class="form-top">
                          <div class="form-top-left">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-onlys">100% Completing</span>
                                </div>
                            </div>
                          </div>

                       </div>
                       <div class="form-bottom">
                        <div class="form-group">
                            <label for="company_id">Comapny Name</label>
                            <div class="controls">
                                <select class="required livesearch form-control" name="company_id" id="edit4_company_id">
                                    <option value="">Company Name</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group nonsalary">
                            <label for="designation">Designation</label>
                            <div class="controls">
                               <input name="designation" placeholder="designation..." class="form-control" id="edit4_designation" type="text">
                            </div>
                         </div>
                         <div class="form-group salary">
                            <label for="company_vintage">Time in current company</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="company_vintage" id="edit4_company_vintage">
                                    <option value="Less Then 1 Year">Less Then 1 Year</option>
                                    <option value="1 Year">1 Year</option>
                                    <option value="2 Years">2 Years</option>
                                    <option value="3 Years">3 Years</option>
                                    <option value="More Then 3 Years">More Then 3 Years</option>
                                </select>
                            </div>
                         </div>
                         <div class="form-group salary">
                            <label for="monthly_salary">Net monthly salary(In hand)</label>
                            <div class="controls">
                               <input name="monthly_salary" placeholder="Net monthly salary..." class="form-control" id="edit4_monthly_salary" type="text">
                            </div>
                         </div>
                         <div class="form-group nonsalary">
                            <label for="office_email">Office Email</label>
                            <div class="controls">
                               <input name="office_email" placeholder="Office Email..." class="form-control" id="edit4_office_email" type="text">
                            </div>
                         </div>
                         <div class="form-group nonsalary">
                            <label for="company_address">Company Address</label>
                            <div class="controls">
                               <input name="company_address" placeholder="Company Address..." class="form-control" id="edit4_company_address" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="pincodes_id">Pincode</label>
                            <div class="controls">
                                <select class="required pincodes_id livesearch form-control" name="pincode_id" id="edit4_pincodes_id">
                                    <option value="">Pincodes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                <select class=" form-control state_id select2_1" name="state_id" id="edit4_state_id">
                                        @foreach ($states as $state)
                                        <option value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class=" form-control city_id select2_1" name="city_id" id="edit4_city_id">
                                    <option>Select City</option>
                            </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lead_remark">Your Remark</label>
                            <div class="controls">
                               <textarea name="lead_remark" placeholder="Your Remark..." class="form-control" id="edit4_lead_remark" maxlength="255" rows="2"></textarea>
                            </div>
                         </div>
                        <div class="form-group">
                            <label for="city_id">Lead Status</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="lead_status" id="edit4_lead_status">
                                    <option value="i">Incomplete</option>
                                    <option value="p">Processing</option>
                                    <option value="c">Closed</option>
                                    <option value="r">Rejected</option>
                            </select>
                            </div>
                        </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="edit_pre3" class="btn btn-default regu btn-prev">Prev</button>
                 <button type="button" id="edit_next4" data-form="edit_form4" data-route="{{ route('pl-income-update') }}" class="btn btn-default regu btn-next">Finish</button>
                 {{-- <button type="button" class="btn btn-default regu" data-dismiss="modal">Close</button> --}}
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal 5-->
     <!-- end lead Modal -->

     <!-- Show lead Modal -->
     <div class="modal fade bd-example-modal-lg" id="showModal3" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="position: relative; overflow-y: auto; height:560px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close edit_vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="showModalLabel">Show Lead Details: <span id="show_product_title"></span></h4>
              </div>
              <div class="modal-body">

                <div class="box-content card">
                    <h4 class="box-title"><i class="fa fa-envira ico"></i>Lead Details</h4>

                    <div class="card-content">
                        <div class="row">
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Lead Status:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_lead_status"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Lead Remark:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_lead_remark"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Lead By:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_lead_by"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Assign To:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_assign_to"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Updated By:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_updated_by"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                    </div>
                    <!-- /.card-content -->
                    <h4 class="box-title"><i class="fa fa-user ico"></i>Owner Details</h4>
                    <div class="card-content">
                        <div class="row">
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Owner Name:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_full_name"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Owner Mobile No:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_mobile_no"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Owner Email:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_email"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Owner DOB:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_dob"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Owner Address:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_address"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Pincode:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_pincode"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>City:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_city"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>State:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_state"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-content -->
                    <h4 class="box-title"><i class="fa fa-file-text ico"></i>Existing Loan Details</h4>
                    <div class="card-content">
                        <div class="row">
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Total EMI:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_total_rem_loan"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Total Loan Amount:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_monthly_emi"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-content -->
                    <h4 class="box-title"><i class="fa fa-file-text ico"></i>Employment Details</h4>
                    <div class="card-content">
                        <div class="row">
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Company Name:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_company_name"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Monthly Salary:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_monthly_salary"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Designation:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_designation"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Company Vintage:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_company_vintage"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Office Email:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_office_email"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Company Address:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_company_address"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-content -->
                </div>

              </div>
              <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
           </div>
        </div>
     </div>
@endsection
