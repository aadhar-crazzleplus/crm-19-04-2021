@extends('crm.crmlayout')

@section('content')
<div class="row">

<div class="col-md-10 col-xs-12">
    @if(session('status'))
        <div class="alert alert-success mb-1 mt-1">
        {{ session('status') }}
        </div>
    @endif
    @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>
                </div>
            @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Error!</strong>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="box-content">
        <h4 class="box-title">Add Admin User</h4>
        <!-- /.box-title -->
        <div class="dropdown js__drop_down">
            <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
            <ul class="sub-menu">
                <li><a href="{{route('admins')}}">All Users</a></li>
                    <li><a href="{{route('add-admin')}}">Add Admin User</a></li>
                    <li><a href="{{route('deleted-admin')}}">Deleted Admin Users</a></li>
            </ul>
            <!-- /.sub-menu -->
        </div>
        <!-- /.dropdown js__dropdown -->
        <form id="commentForm" action="{{ route('store-admin') }}" method="POST" enctype="multipart/form-data" autocomplete="off">

            @csrf
            <div id="tabsleft" class="tabbable tabs-left">
                <ul>
                    <li><a href="#tabsleft-tab1" data-toggle="tab">Personal Details</a></li>
                    <li><a href="#tabsleft-tab2" data-toggle="tab">Bank Details</a></li>
                    <li><a href="#tabsleft-tab3" data-toggle="tab">Educational Details</a></li>
                    <li><a href="#tabsleft-tab4" data-toggle="tab">Professional Details</a></li>
                    <li><a href="#tabsleft-tab5" data-toggle="tab">Kyc Details</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tabsleft-tab1">
                        <div class="form-group">
                            <label for="are_you">Are You</label>
                            <div class="controls">
                                <select class="required form-control " name="are_you" id="are_you">
                                    <option value="sole">Individual</option>
                                    <option value="company">Non Individual</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="firm_names" style="display: none;">
                            <label for="firm_name">Firm Name</label>
                            <div class="controls">
                                <input type="text" id="firm_name" name="firm_name" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_type">User Type</label>
                            <div class="controls">
                                @if (auth("admin")->user()->user_type_id == "1")
                                <select class="required form-control " name="user_type_id" id="user_type_id">
                                    <option value="">Select User Type</option>
                                    @foreach ($user_types as $user_type)
                                    <option value="{{$user_type->id}}">{{$user_type->title}}</option>
                                    @endforeach
                                </select>
                                @else
                                <select class="required form-control " name="user_type_id" id="user_type_id">
                                    <option value="">Select User Type</option>
                                    @foreach ($user_types as $user_type)
                                    @if ($user_type->id > auth("admin")->user()->user_type_id)
                                    <option value="{{$user_type->id}}">{{$user_type->title}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @endif

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="controls">
                                <input type="text" id="email" name="email" class="required email form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <div class="controls">
                                <input type="text" id="first_name" name="first_name" class="required form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <div class="controls">
                                <input type="text" id="last_name" name="last_name" class="required form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mobile_no">Mobile Number</label>
                            <div class="controls">
                                <input type="text" id="mobile_no" name="mobile_no" class="required form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <div class="controls">
                                <select class="required form-control " name="gender" id="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="m">Male</option>
                                    <option value="f">Female</option>
                                    <option value="o">Others</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="dob">DOB</label>
                            <div class="controls">
                                <div class="input-group">
                                    <input type="text" class=" form-control datepicker" placeholder="dd/mm/yyyy" name="dob" id="dob">
                                    <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add1">Address</label>
                            <div class="controls">
                                <input type="text" id="add1" name="add1" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pincode_id">Pincode</label>
                            <div class="controls">

                                {{-- <input type="text" placeholder="All India Pincodes" class=" form-control flexdatalist" data-min-length="1" id="pincode_id" name="pincode_id" value="" autocomplete="false"> --}}
                                <select class="required livesearch form-control" name="pincode_id" id="pincode_id">
                                    <option value="">Pincodes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="state_id" id="state_id">
                                        @foreach ($states as $state)
                                        <option value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class=" form-control select2_1" name="city_id" id="city_id">
                                    <option>Select City</option>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_current">Is this current address?</label>
                            <div class="controls">
                                <div class="radio">
                                    <input type="radio" value="y" name="is_current" id="is_current" >
                                    <label for="is_current">Yes</label>
                                </div>
                                <div class="radio">
                                    <input type="radio" value="n" name="is_current" id="is_current2" >
                                    <label for="is_current2">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nominee_name">Nominee Name</label>
                            <div class="controls">
                                <input type="text" id="nominee_name" name="nominee_name" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nominee_relation">Nominee Relation</label>
                            <div class="controls">
                                <input type="text" id="nominee_relation" name="nominee_relation" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nominee_dob">Nominee DOB</label>
                            <div class="controls">
                                <div class="input-group">
                                    <input type="text" class=" form-control datepicker" placeholder="dd/mm/yyyy" name="nominee_dob" id="nominee_dob">
                                    <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="tabsleft-tab2">
                        <div class="form-group">
                            <label for="name_on_bank">Name as per Bank Record</label>
                            <div class="controls">
                                <input type="text" id="name_on_bank" name="name_on_bank" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bank_id">Select Bank</label>
                            <div class="controls">
                                <select class=" form-control" name="bank_id" id="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                    <option value="{{$bank->id}}">{{$bank->bank_title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="account_no">Account No</label>
                            <div class="controls">
                                <input type="text" id="account_no" name="account_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ifsc_code">ifsc Code</label>
                            <div class="controls">
                                <input type="text" id="ifsc_code" name="ifsc_code" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="uploads">Select Doc to Upload</label>
                            <div class="controls">
                                <select class=" form-control " name="uploads" id="uploads">
                                    <option value="">Select Doc Type</option>
                                    <option value="cheque">Cheque book</option>
                                    <option value="pass">Passbook</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="urlfield">Upload Selected Doc</label>
                            <div class="controls">
                                <input type="file" id="upload_doc" name="upload_doc" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="tabsleft-tab3">
                        <div class="form-group">
                            <label for="qualification">Select Qualifications</label>
                            <div class="controls">
                                <select class=" form-control " name="qualification" id="qualification">
                                    <option value="">Select Qualifications</option>
                                    <option value="hs">High School</option>
                                    <option value="ug">undergraduate</option>
                                    <option value="pg">postgraduate</option>
                                    <option value="pr">professionals</option>
                                    <option value="ot">others</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload_qual_doc">Upload Certificate/Marksheet</label>
                            <div class="controls">
                                <input type="file" id="upload_qual_doc" name="upload_qual_doc" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabsleft-tab4">
                        <div class="form-group">
                            <label for="pos_income_id">Primary Source of income</label>
                            <div class="controls">
                                <select class=" form-control " name="pos_income_id" id="pos_income_id">
                                    <option value="">Select Income Source</option>
                                    @foreach ($pos_incomes as $pos_income)
                                    <option value="{{$pos_income->id}}">{{$pos_income->title}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total_fn_yr">Total Experience in any financial product</label>
                            <div class="controls">
                                <div class="col-md-4">Years
                                    <select class=" form-control " name="total_fn_yr" id="total_fn_yr">
                                        @for($i=0; $i<=40; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">Months
                                    <select class=" form-control " name="total_fn_month" id="total_fn_month">
                                        @for($i=0; $i<=12; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="office_space">Do you have an office space to sell financial products?</label>
                            <div class="controls">
                                <div class="radio">
                                    <input type="radio" value="y" name="office_space" id="office_space" >
                                    <label for="office_space">Yes</label>
                                </div>
                                <div class="radio">
                                    <input type="radio" value="n" name="office_space" id="office_space2" >
                                    <label for="office_space2">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="did_sell">Which of these products did you sell?</label>
                            <div class="controls">
                                @foreach ($fin_products as $fin_product)
                                    <div class="checkbox">
                                        <input type="checkbox" id="{{strtolower(str_replace(' ','_',$fin_product->product_name))}}" name="did_sell[]" value="{{$fin_product->id}}">
                                        <label for="{{strtolower(str_replace(' ','_',$fin_product->product_name))}}">{{$fin_product->product_name}}</label>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total_ex_yr">Have Agency or POS License?</label>
                            <div class="controls">
                                <div class="radio">
                                    <input type="radio" value="y" name="pos_licence" id="pos_licence" >
                                    <label for="pos_licence">Yes</label>
                                </div>
                                <div class="radio">
                                    <input type="radio" value="n" name="pos_licence" id="pos_licence2" >
                                    <label for="pos_licence2">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total_bus_anum">Total Finatial Business Income per anum</label>
                            <div class="controls">
                                <input type="text" id="total_bus_anum" name="total_bus_anum" class=" form-control">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabsleft-tab5">
                        <div class="form-group">
                            <label for="add_proof">Select Address Proof to be upload</label>
                            <div class="controls">
                                <select class=" form-control " name="add_proof" id="add_proof">
                                    <option value="">Select Address Proof</option>
                                    <option value="p">Passport</option>
                                    <option value="d">Driving Licence</option>
                                    <option value="a">UID Aadhar</option>
                                    <option value="v">Voter Id</option>
                                    <option value="r">ration card</option>
                                    <option value="o">others</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add_proof_no">Address Proof (ID) Number</label>
                            <div class="controls">
                                <input type="text" id="add_proof_no" name="add_proof_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_doc_front">Upload Address Proof Front and Back</label>
                            <div class="controls">
                                <input type="file" id="id_doc_front" name="id_doc_front" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                            <div class="controls">
                                <input type="file" id="id_doc_back" name="id_doc_back" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pan_no">PAN Number</label>
                            <div class="controls">
                                <input type="text" id="pan_no" name="pan_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload_pan_no">Upload PAN Card</label>
                            <div class="controls">
                                <input type="file" id="upload_pan_no" name="upload_pan_no" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gst_no">GST Number</label>
                            <div class="controls">
                                <input type="text" id="gst_no" name="gst_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload_gst_no">Upload GST Doc</label>
                            <div class="controls">
                                <input type="file" id="upload_gst_no" name="upload_gst_no" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>
                    </div>
                    <ul class="pager wizard">
                        <li class="previous first"><a href="javascript:;">First</a></li>
                        <li class="previous"><a href="javascript:;">Previous</a></li>
                        <li class="next last"><a href="javascript:;">Last</a></li>
                        <li class="next"><a href="javascript:;">Next</a></li>
                        <li class="finish"><a href="javascript:;">Finish</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
    <input type="hidden" name="temp" id="temps" >
    <!-- /.box-content -->
</div>
<!-- /.col-md-6 col-xs-12 -->
</div>
<script>

$(document).ready(function () {
    $('#state_id').on('change', function() {
        var state_id = this.value;
        $("#city_id").html('');
        $.ajax({
            url:"{{url('getcity')}}",
            type: "POST",
            data: {
            state_id: state_id,
            _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result){
                $('#city_id').html('<option>Select City</option>');
                $.each(result,function(key,value){
                    $("#city_id").append('<option value="'+value.id+'">'+value.city_name+'</option>');
                });
                var temps = $('#temps').val();
                $('#city_id').val(temps).trigger('change');
            }
        });
    });

    $('#are_you').on('change', function() {
        if(this.value == "sole"){
            $("#firm_names").hide();
        }else{
            $("#firm_names").show();
        }
    });
    $('#pincode_id').on('change', function() {
        var pincode_id = this.value;
        $("#city_id").html('');
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
                // console.log(result);alert("sssss"+result.citystate_id.city_id);
                // $('#city_id').html('<option value="">Select City</option>');
                // $.each(result.cities,function(key,value){
                //     $("#city_id").append('<option value="'+value.id+'">'+value.city_name+'</option>');
                // });
                $('#temps').val(result.citystate_id.city_id);
                $('#state_id').val(result.citystate_id.state_id).trigger('change');

            }
        });
    });

});

</script>

@endsection

