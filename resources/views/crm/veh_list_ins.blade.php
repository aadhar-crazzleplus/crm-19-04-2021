@extends('crm.crmlayout')

@section('content')

<div class="row small-spacing">
    <div class="col-xs-12">
        <div class="box-content">
            <h4 class="box-title">Default</h4>
            <!-- /.box-title -->
            <div class="dropdown js__drop_down">
                <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
                <ul class="sub-menu">
                    <li><a href="{{route('vehicle-ins')}}">All</a></li>
                    {{-- <li><a data-toggle="modal" href="{{route('veh-add-ins')}}">Add News</a></li> --}}
                    <li><a data-toggle="modal" data-target="#myModal1" href="#myModal1">Add News</a></li>
                    <li><a href="{{route('veh-deleted-ins')}}">Deleted List</a></li>
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
                        <th>Owner Name</th>
                        <th>Vehicle No</th>
                        <th>Assign To</th>
                        <th>Mobile No</th>
                        <th>Vehicle Info</th>
                        <th>Status</th>
                        <th>Updated at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Owner Name</th>
                        <th>Vehicle No</th>
                        <th>Assign To</th>
                        <th>Mobile No</th>
                        <th>Vehicle Info</th>
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
        ajax: "{{route('getleads')}}",
        columns: [
        { data: 'full_name' },
        { data: 'regn_no' },
        { data: 'assign_to' },
        { data: 'mobile_no' },
        { data: 'maker_desc' },
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
    });
    $(".edit_vhclose").on("click", function(){
        table.ajax.reload();

        $('#edit_form2')[0].reset();
        $('#edit_form3')[0].reset();
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
            $.ajax({
                url: $(this).data("route"),
                method: 'post',
                // data: {
                //     mobile_no: $('#mobile_no').val(),
                //     regn_no: $('#regn_no').val(),
                //     _token: '{{csrf_token()}}'
                // },
                data: $('#'+froms).serialize(),
                success: function(result) {
                    $(".regu").show();
                    $(".processing").hide();
                    // console.log('Error:', result);
                    if(result.success) {
                        toastr["success"]("Performed !");
                        currentModal.modal('hide');
                        currentModal.closest("div[id^='myModal']").nextAll("div[id^='myModal']").first().modal('show');
                        if(froms == "form1"){
                            $("#form2_profile_id").val(result.data.profile_id);
                            $("#form2_address_id").val(result.data.address_id);
                            $("#form2_lead_id").val(result.data.lead_id);
                            $("#vehicle_id").val(result.data.vehicle_id);
                            $("#maker_desc").val(result.data.maker_desc);
                            $("#maker_model").val(result.data.maker_model);
                            $("#fuel_desc").val(result.data.fuel_desc);
                            $("#registered_at").val(result.data.registered_at);
                            $('#regn_dt').val(result.data.regn_dt).trigger('change');
                            $("#insurance_upto").val(result.data.insurance_upto);
                        }
                        if(froms == "form2"){
                            getpincodebyid(result.data.address.pincode_id,'pincodes_id');
                            $("#profile_id").val(result.data.profile.profile_id);
                            $("#address_id").val(result.data.address.address_id);
                            $("#lead_id").val(result.data.profile.lead_id);
                            $("#full_name").val(result.data.profile.full_name);
                            $("#mobile_no").val(result.data.profile.mobile_no);
                            $("#email").val(result.data.profile.email);
                            $("#dob").val(result.data.profile.dob);
                            $("#address").val(result.data.address.address);
                            // $("#city_id").val(result.data.address.city_id);
                            // $("#state_id").val(result.data.address.state_id);
                        }
                        if(froms == "form3"){
                            table.ajax.reload();
                            $('#form1')[0].reset();
                            $('#form2')[0].reset();
                            $('#form3')[0].reset();
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
                    // toastr["error"]("Inconceivable! <br> ssss");
                    // alert("dddd"+data.responseJSON.success);
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
        $.ajax({
            url: $(this).data("route"),
            method: 'post',
            // data: {
            //     mobile_no: $('#mobile_no').val(),
            //     regn_no: $('#regn_no').val(),
            //     _token: '{{csrf_token()}}'
            // },
            data: $('#'+froms).serialize(),
            success: function(result) {
                $(".regu").show();
                    $(".processing").hide();
                // console.log('Error:', result);
                if(result.success) {
                    toastr["success"]("Performed !");
                    currentEditModal.modal('hide');
                    currentEditModal.closest("div[id^='editModal']").nextAll("div[id^='editModal']").first().modal('show');

                    if(froms == "edit_form2"){
                        getpincodebyid(result.data.pincode_id, 'edit_pincodes_id');
                        $("#edit2_profile_id").val(result.data.profile_id);
                        $("#edit2_address_id").val(result.data.address_id);
                        $("#edit2_lead_id").val(result.data.lead_id);
                        $("#edit_full_name").val(result.data.full_name);
                        $("#edit_mobile_no").val(result.data.mobile_no);
                        $("#edit_email").val(result.data.email);
                        $("#edit_dob").val(result.data.dob);
                        $("#edit_address").val(result.data.address);
                        $("#lead_remark").val(result.data.lead_remark);
                        $("#lead_status").val(result.data.lead_status).trigger('change');

                        $("#edit2_lead_by").append('<option value="'+result.data.lead_by_id+'">'+result.data.lead_by+'</option>');
                        $('#edit2_lead_by').val(result.data.lead_by_id).trigger('change');

                        $("#edit2_assign_to").append('<option value="'+result.data.assign_to_id+'">'+result.data.assign_to+'</option>');
                        $('#edit2_assign_to').val(result.data.assign_to_id).trigger('change');

                        // $('#regn_dt').val(result.data.regn_dt)
                        // $("#city_id").val(result.data.address.city_id);
                        // $("#state_id").val(result.data.address.state_id);
                    }
                    if(froms == "edit_form3"){
                        table.ajax.reload();
                        $('#edit_form2')[0].reset();
                        $('#edit_form3')[0].reset();
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
                // toastr["error"]("Inconceivable! <br> ssss");
                // alert("dddd"+data.responseJSON.success);
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
    });
    });


    $('.state_id').on('change', function() {
        var state_id = this.value;
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

function editLead(edit_route){
    $.ajax({
        url:edit_route,
        type: "GET",
        // data: {
        //     _token: '{{csrf_token()}}'
        // },
        dataType : 'json',
        success: function(result){
            $("#edit_profile_id").val(result.data.profile_id);
            $("#edit_address_id").val(result.data.address_id);
            $("#edit_lead_id").val(result.data.lead_id);
            $("#edit_lead_remark").val(result.data.lead_remark);
            $("#edit_lead_status").val(result.data.lead_status);
            $("#edit_vehicle_id").val(result.data.vehicle_id);
            $("#edit_maker_desc").val(result.data.maker_desc);
            $("#edit_maker_model").val(result.data.maker_model);
            $("#edit_fuel_desc").val(result.data.fuel_desc);
            $("#edit_registered_at").val(result.data.registered_at);
            $('#edit_regn_dt').val(result.data.regn_dt).trigger('change');
            $("#edit_insurance_upto").val(result.data.insurance_upto);

            $("#edit_lead_by").val(result.data.lead_by);
            $("#edit_lead_by_id").val(result.data.lead_by_id);
            $("#edit_assign_to").val(result.data.assign_to);
            $("#edit_assign_to_id").val(result.data.assign_to_id);
        }
    });
}

function showLead(id){
    $.ajax({
        url:"{{route('veh-show-ins')}}",
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

            $("#show_regn_no").html(result.data.regn_no);
            $("#show_maker_desc").html(result.data.maker_desc);
            $("#show_maker_model").html(result.data.maker_model);
            $("#show_vh_class_desc").html(result.data.vh_class_desc);
            $("#show_body_type_desc").html(result.data.body_type_desc);
            $("#show_fuel_desc").html(result.data.fuel_desc);
            $("#show_fit_upto").html(result.data.fit_upto);
            $("#show_insurance_comp").html(result.data.insurance_comp);
            $("#show_insurance_policy_no").html(result.data.insurance_policy_no);
            $("#show_registered_at").html(result.data.registered_at);
            $("#show_regn_dt").html(result.data.regn_dt);
            $("#show_insurance_upto").html(result.data.insurance_upto);
            $("#show_manu_month_yr").html(result.data.manu_month_yr);
            $("#show_owner_sr").html(result.data.owner_sr);
            $("#show_financer").html(result.data.financer);
            $("#show_status_as_on").html(result.data.status_as_on);
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
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%">
                                    <span class="sr-onlys">33% Completing</span>
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
                                <input name="mobile_no" placeholder="Mobile number..." class="form-control input-error" id="form2_mobile_no" type="number" max="10" maxlength="10">
                             </div>
                          </div>
                          <div class="form-group">
                             <label for="regn_no">Registration No</label>
                             <div class="controls">
                                <input  style="text-transform: uppercase;" name="regn_no" placeholder="Registration no..." class="form-control input-error" id="regn_no" type="text" minlength="7" maxlength="10">
                             </div>
                          </div>
                          <div class="form-group">
                            <label for="lead_by">Lead By</label>
                            <div class="controls">
                                <select class="required lead_by livesearch form-control" name="lead_by" id="lead_by">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="assign_to">Assign To</label>
                            <div class="controls">
                                <select class="required assign_to livesearch form-control" name="assign_to" id="assign_to">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>

                          {{-- <button type="button" class="btn btn-next">Next</button> --}}
                       </div>
                    </fieldset>

                 </form>
              </div>
              <div class="modal-footer">
                 {{-- <button type="button" class="btn btn-default btn-prev">Prev</button> --}}
                 <button type="button" id="next1" data-form="form1" data-route="{{ route('veh-reg-ins') }}" class="btn btn-default regu btn-next">Next</button>
                 <button type="button" class="btn btn-default regu vhclose" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- Add Modal -->
     <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog  modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:600px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Vehicle Details</h4>
              </div>
              <div class="modal-body">
                <form id="form2" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="form2_profile_id" value="" />
                    <input type="hidden" name="address_id" id="form2_address_id" value="" />
                    <input type="hidden" name="lead_id" id="form2_lead_id" value="" />
                    <input type="hidden" name="vehicle_id" id="vehicle_id" value="" />

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
                             <label for="maker_desc">Vehicle Manufacturer</label>
                             <div class="controls">
                                <input name="maker_desc" placeholder="Vehicle Manufacturer..." class="form-control" id="maker_desc" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                             <label for="maker_model">Vehicle Model</label>
                             <div class="controls">
                                <input name="maker_model" placeholder="Vehicle Model..." class="form-control" id="maker_model" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                             <label for="fuel_desc">Vehicle Variant</label>
                             <div class="controls">
                                <input name="fuel_desc" placeholder="Vehicle Variant..." class="form-control" id="fuel_desc" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                             <label for="registered_at">Registration Location</label>
                             <div class="controls">
                                <input name="registered_at" placeholder="Registration Location..." class="form-control" id="registered_at" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                            <label for="regn_dt">Registration Year</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="regn_dt" id="regn_dt">
                                    @foreach (range(date("Y"), date("Y")-20) as $year)
                                    <option value="{{$year}}">{{$year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                          <div class="form-group">
                             <label for="insurance_upto">Policy Expiry Date</label>
                             <div class="controls">
                                <input name="insurance_upto" class="form-control datepicker" placeholder="dd-mm-yyyy" id="insurance_upto" type="text">
                             </div>
                          </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="pre1" class="btn regu btn-default btn-prev">Prev</button>
                 <button type="button" id="next2" data-form="form2" data-route="{{ route('veh-details-ins') }}" class="btn regu btn-default btn-next">Next</button>
                 <button type="button" class="btn btn-default regu vhclose" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- add Modal -->
     <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:600px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Owner Details</h4>
              </div>
              <div class="modal-body">
                <form id="form3" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="profile_id" value="" />
                    <input type="hidden" name="address_id" id="address_id" value="" />
                    <input type="hidden" name="lead_id" id="lead_id" value="" />
                    <input type="hidden" name="temp" id="temps" >
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
                            <label for="full_name">Owner Name</label>
                            <div class="controls">
                               <input name="full_name" placeholder="Owner Name..." class="form-control" id="full_name" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="mobile_no">Owner Mobile Number</label>
                            <div class="controls">
                               <input name="mobile_no" placeholder="Owner Mobile No..." class="form-control" id="mobile_no" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="email">Owner Email</label>
                            <div class="controls">
                               <input name="email" placeholder="Owner email..." class="form-control" id="email" type="email">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="dob">Owner DOB</label>
                            <div class="controls">
                                <input name="dob" class="form-control datepicker" placeholder="dd-mm-yyyy" id="dob" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="address">Owner Address</label>
                            <div class="controls">
                               <input name="address" placeholder="Owner address..." class="form-control" id="address" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="pincodes_id">Pincode</label>
                            <div class="controls">
                                <select class="required pincodes_id livesearch form-control" name="pincodes_id" id="pincodes_id">
                                    <option value="">Pincodes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                <select class=" form-control state_id select2_1" name="state_id" id="state_id">
                                        @foreach ($states as $state)
                                        <option value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class=" form-control city_id select2_1" name="city_id" id="city_id">
                                    <option>Select City</option>
                            </select>
                            </div>
                        </div>
                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 <button type="button" id="pre2" class="btn regu btn-default btn-prev">Prev</button>
                 <button type="button" id="next3" data-form="form3" data-route="{{ route('veh-profile-ins') }}" class="btn regu btn-default btn-next">Finish</button>
                 {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- Edit Modal -->
     <div class="modal fade" id="editModal2" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog  modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:600px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close edit_vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="editModalLabel">Vehicle Details</h4>
              </div>
              <div class="modal-body">
                <form id="edit_form2" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="edit_profile_id" value="" />
                    <input type="hidden" name="address_id" id="edit_address_id" value="" />
                    <input type="hidden" name="lead_id" id="edit_lead_id" value="" />
                    <input type="hidden" name="lead_remark" id="edit_lead_remark" value="" />
                    <input type="hidden" name="lead_status" id="edit_lead_status" value="" />
                    <input type="hidden" name="vehicle_id" id="edit_vehicle_id" value="" />
                    <input type="hidden" name="edit_rout" id="edit_rout" value="" />

                    <input type="hidden" name="lead_by" id="edit_lead_by" value="" />
                    <input type="hidden" name="lead_by_id" id="edit_lead_by_id" value="" />
                    <input type="hidden" name="assign_to" id="edit_assign_to" value="" />
                    <input type="hidden" name="assign_to_id" id="edit_assign_to_id" value="" />

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
                             <label for="maker_desc">Vehicle Manufacturer</label>
                             <div class="controls">
                                <input name="maker_desc" placeholder="Vehicle Manufacturer..." class="form-control" id="edit_maker_desc" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                             <label for="maker_model">Vehicle Model</label>
                             <div class="controls">
                                <input name="maker_model" placeholder="Vehicle Model..." class="form-control" id="edit_maker_model" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                             <label for="fuel_desc">Vehicle Variant</label>
                             <div class="controls">
                                <input name="fuel_desc" placeholder="Vehicle Variant..." class="form-control" id="edit_fuel_desc" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                             <label for="registered_at">Registration Location</label>
                             <div class="controls">
                                <input name="registered_at" placeholder="Registration Location..." class="form-control" id="edit_registered_at" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                            <label for="regn_dt">Registration Year</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="regn_dt" id="edit_regn_dt">
                                    @foreach (range(date("Y"), date("Y")-20) as $year)
                                    <option value="{{$year}}">{{$year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                          <div class="form-group">
                             <label for="insurance_upto">Policy Expiry Date</label>
                             <div class="controls">
                                <input name="insurance_upto" class="form-control datepicker" placeholder="dd-mm-yyyy" id="edit_insurance_upto" type="text">
                             </div>
                          </div>

                       </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 {{-- <button type="button" id="edit_pre1" class="btn btn-default btn-prev">Prev</button> --}}
                 <button type="button" id="edit_next2" data-form="edit_form2" data-route="{{ route('veh-update-veh-ins') }}" class="btn regu btn-default btn-next">Next</button>
                 <button type="button" class="btn btn-default regu edit_vhclose" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- Edit Modal -->
     <div class="modal fade" id="editModal3" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:600px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close edit_vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="editModalLabel">Owner Details</h4>
              </div>
              <div class="modal-body">
                <form id="edit_form3" action="" method="post">
                    @csrf
                    <input type="hidden" name="profile_id" id="edit2_profile_id" value="" />
                    <input type="hidden" name="address_id" id="edit2_address_id" value="" />
                    <input type="hidden" name="lead_id" id="edit2_lead_id" value="" />
                    <input type="hidden" name="temp" id="edit_temps" >
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
                            <label for="full_name">Owner Name</label>
                            <div class="controls">
                               <input name="full_name" placeholder="Owner Name..." class="form-control" id="edit_full_name" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="mobile_no">Owner Mobile Number</label>
                            <div class="controls">
                               <input name="mobile_no" placeholder="Owner Mobile No..." class="form-control" id="edit_mobile_no" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="email">Owner Email</label>
                            <div class="controls">
                               <input name="email" placeholder="Owner email..." class="form-control" id="edit_email" type="email">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="dob">Owner DOB</label>
                            <div class="controls">
                                <input name="dob" class="form-control datepicker" placeholder="dd-mm-yyyy" id="edit_dob" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="address">Owner Address</label>
                            <div class="controls">
                               <input name="address" placeholder="Owner address..." class="form-control" id="edit_address" type="text">
                            </div>
                         </div>
                         <div class="form-group">
                            <label for="pincodes_id">Pincode</label>
                            <div class="controls">
                                <select class="required pincodes_id livesearch form-control" name="pincodes_id" id="edit_pincodes_id">
                                    <option value="">Pincodes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                <select class=" form-control state_id select2_1" name="state_id" id="edit_state_id">
                                        @foreach ($states as $state)
                                        <option value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class=" form-control city_id select2_1" name="city_id" id="edit_city_id">
                                    <option>Select City</option>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lead_by">Lead By</label>
                            <div class="controls">
                                <select class="required lead_by livesearch form-control" name="lead_by" id="edit2_lead_by">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="assign_to">Assign To</label>
                            <div class="controls">
                                <select class="required assign_to livesearch form-control" name="assign_to" id="edit2_assign_to">
                                    <option value="">Select Advisor</option>
                                </select>
                            </div>
                          </div>
                        <div class="form-group">
                            <label for="lead_remark">Your Remark</label>
                            <div class="controls">
                               <textarea name="lead_remark" placeholder="Your Remark..." class="form-control" id="lead_remark" maxlength="255" rows="2"></textarea>
                            </div>
                         </div>
                        <div class="form-group">
                            <label for="city_id">Lead Status</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="lead_status" id="lead_status">
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
                 <button type="button" id="edit_pre2" class="btn regu btn-default btn-prev">Prev</button>
                 <button type="button" id="edit_next3" data-form="edit_form3" data-route="{{ route('veh-update-pro-ins') }}" class="btn regu btn-default btn-next">Finish</button>
                 {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>


     <!-- Show lead Modal -->
     <div class="modal fade bd-example-modal-lg" id="showModal3" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="position: relative; overflow-y: auto; height:600px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="showModalLabel">Show Lead Details: <span id="show_product_title"></span></h4>
              </div>
              <div class="modal-body">

                <div class="box-content card">
                    <h4 class="box-title"><i class="fa fa-envira ico"></i>Lead Details</h4>
                    <!-- /.box-title -->
                    {{-- <div class="dropdown js__drop_down">
                        <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
                        <ul class="sub-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else there</a></li>
                            <li class="split"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                        <!-- /.sub-menu -->
                    </div> --}}
                    <!-- /.dropdown js__dropdown -->
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
                    <h4 class="box-title"><i class="fa fa-automobile ico"></i>Vehicle Details</h4>
                    <div class="card-content">
                        <div class="row">
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Registration No:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_regn_no"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Vehicle Manufacturer:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_maker_desc"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Vehicle Model:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_maker_model"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Vehicle Class:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_vh_class_desc"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Body Type:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_body_type_desc"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Fuel Type:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_fuel_desc"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Fitness Upto:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_fit_upto"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row text-success">
                                    <div class="col-xs-5"><label>Insurance Company:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_insurance_comp"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Policy No:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_insurance_policy_no"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Registered At:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_registered_at"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Registartion Year:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_regn_dt"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row text-danger">
                                    <div class="col-xs-5"><label>Insurance Expiry Date:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_insurance_upto"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Manufacturing Month/Year:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_manu_month_yr"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Owner No:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_owner_sr"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row text-primary">
                                    <div class="col-xs-5"><label>Finance From:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_financer"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Record Fetched On:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_status_as_on"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                    </div>
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
                </div>

              </div>
              <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
           </div>
        </div>
     </div>
@endsection
