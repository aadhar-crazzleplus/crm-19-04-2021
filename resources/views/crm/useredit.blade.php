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
        <h4 class="box-title">Edit User</h4>
        <!-- /.box-title -->
        <div class="dropdown js__drop_down">
            <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
            <ul class="sub-menu">
                <li><a href="{{route('crm.users')}}">All Users</a></li>
                <li><a href="{{route('crm.add-user')}}">Add User</a></li>
                <li><a href="{{route('crm.deleted')}}">Deleted Users</a></li>
            </ul>
            <!-- /.sub-menu -->
        </div>
        <!-- /.dropdown js__dropdown -->
        <form id="commentForm" action="{{ route('crm.update-user',$users->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">

            @csrf
        <input type="hidden" name="user_id" value="{{$users->id}}">
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
                                    <option {{($users->are_you=='sole')?'selected':''}} value="sole">Individual</option>
                                    <option {{($users->are_you=='company')?'selected':''}} value="company">Non Individual</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="firm_names" style="display: {{($users->are_you=='company')?'block':'none'}};">
                            <label for="firm_name">Firm Name</label>
                            <div class="controls">
                                <input type="text" id="firm_name" name="firm_name" value="{{$users->firm_name??""}}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_type">User Type</label>
                            <div class="controls">
                                <select class="required form-control " name="user_type_id" id="user_type_id">
                                    <option value="">Select User Type</option>
                                    @foreach ($user_types as $user_type)
                                    <option {{($users->user_type_id==$user_type->id)?'selected':''}} value="{{$user_type->id}}">{{$user_type->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="controls">
                                <input type="text" id="email" name="email" value="{{$users->email}}" class="required email form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <div class="controls">
                                <input type="text" id="first_name" value="{{$users->first_name}}" name="first_name" class="required form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <div class="controls">
                                <input type="text" id="last_name" value="{{$users->last_name}}" name="last_name" class="required form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mobile_no">Mobile Number</label>
                            <div class="controls">
                                <input type="text" id="mobile_no" value="{{$users->mobile_no}}" name="mobile_no" class="required form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <div class="controls">
                                <select class="required form-control " name="gender" id="gender">
                                    <option value="">Select Gender</option>
                                    <option {{($users->gender=='m')?'selected':''}} value="m">Male</option>
                                    <option {{($users->gender=='f')?'selected':''}} value="f">Female</option>
                                    <option {{($users->gender=='o')?'selected':''}} value="o">Others</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="dob">DOB</label>
                            <div class="controls">
                                <div class="input-group">
                                    <input type="text" value="{{date('d-m-Y',strtotime($users->dob))}}" class=" form-control datepicker" placeholder="dd/mm/yyyy" name="dob" id="dob">
                                    <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add1">Address</label>
                            <div class="controls">
                                <input type="text" id="add1" value="{{$users->address->add1??""}}" name="add1" class="required form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pincode_id">Pincode</label>
                            <div class="controls">

                                {{-- <input type="text" data-value-property='id' placeholder="All India Pincodes" value="{{$users->address->pincode_id}}" class=" form-control flexdatalist" data-min-length="1" id="pincode_id" name="pincode_id" value="" autocomplete="false"> --}}
                                <select class="required livesearch form-control" name="pincode_id" id="pincode_id">
                                    <option selected value="{{$users->address->pincode_id??""}}">{{$users->address->pincode->pincode??""}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <div class="controls">
                                @php
                                    $state_id = $users->address->state_id??'';
                                    $city_id = $users->address->city_id??'';
                                    $is_current = $users->address->is_current??'';
                                    $bank_id = $users->relbank->bank_id??'';
                                @endphp
                                <select class="required form-control select2_1" name="state_id" id="state_id">
                                        @foreach ($states as $state)
                                        <option {{($state->id==$state_id)?'selected':''}} value="{{$state->id}}">{{$state->state_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <div class="controls">
                                <select class="required form-control select2_1" name="city_id" id="city_id">
                                    @foreach ($cities as $city)
                                        <option {{($city->id==$city_id)?'selected':''}} value="{{$city->id}}">{{$city->city_name}}</option>
                                    @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_current">Is this current address?</label>
                            <div class="controls">
                                <div class="radio">
                                    <input type="radio" {{('y'==$is_current)?'checked':''}} value="y" name="is_current" id="is_current" >
                                    <label for="is_current">Yes</label>
                                </div>
                                <div class="radio">
                                    <input type="radio" {{('n'==$is_current)?'checked':''}} value="n" name="is_current" id="is_current2" >
                                    <label for="is_current2">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nominee_name">Nominee Name</label>
                            <div class="controls">
                                <input type="text" id="nominee_name" value="{{$users->nominee_name}}" name="nominee_name" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nominee_relation">Nominee Relation</label>
                            <div class="controls">
                                <input type="text" id="nominee_relation" value="{{$users->nominee_relation}}" name="nominee_relation" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nominee_dob">Nominee DOB</label>
                            <div class="controls">
                                <div class="input-group">
                                    <input type="text" value="{{date('d-m-Y',strtotime($users->nominee_dob))}}" class=" form-control datepicker" placeholder="dd/mm/yyyy" name="nominee_dob" id="nominee_dob">
                                    <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="tabsleft-tab2">
                        <div class="form-group">
                            <label for="name_on_bank">Name as per Bank Record</label>
                            <div class="controls">
                                <input type="text" value="{{$users->relbank->name_on_bank??''}}" id="name_on_bank" name="name_on_bank" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bank_id">Select Bank</label>
                            <div class="controls">
                                <select class=" form-control" name="bank_id" id="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                    <option {{($bank->id==$bank_id)?'selected':''}} value="{{$bank->id}}">{{$bank->bank_title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="account_no">Account No</label>
                            <div class="controls">
                                <input type="text" value="{{$users->relbank->account_no??''}}" id="account_no" name="account_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ifsc_code">ifsc Code</label>
                            <div class="controls">
                                <input type="text" id="ifsc_code" value="{{$users->relbank->ifsc_code??''}}" name="ifsc_code" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            @php
                                $uploads = $users->relbank->uploads??'';
                            @endphp
                            <label for="uploads">Select Doc to Upload</label>
                            <div class="controls">
                                <select class=" form-control " name="uploads" id="uploads">
                                    <option value="">Select Doc Type</option>
                                    <option {{('cheque'==$uploads)?'selected':''}} value="cheque">Cheque book</option>
                                    <option {{('pass'==$uploads)?'selected':''}} value="pass">Passbook</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="urlfield">Upload Selected Doc</label>
                            <div class="controls">
                                {{-- data-default-file="{{public_path($users->upload_qual_doc)}}" --}}
                                @php
                                    $upload_doc=$users->relbank->upload_doc??'';
                                @endphp
                            <input type="file" data-default-file="{{asset('storage/advisors/'.$users->id.'/'.$upload_doc)}}" id="upload_doc" name="upload_doc" class="dropify" data-max-file-size="20M" data-max-height="2000" />
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
                                    <option {{($users->qualification=='hs')?'selected':''}} value="hs">High School</option>
                                    <option {{($users->qualification=='ug')?'selected':''}} value="ug">undergraduate</option>
                                    <option {{($users->qualification=='pg')?'selected':''}} value="pg">postgraduate</option>
                                    <option {{($users->qualification=='pr')?'selected':''}} value="pr">professionals</option>
                                    <option {{($users->qualification=='ot')?'selected':''}} value="ot">others</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload_qual_doc">Upload Certificate/Marksheet</label>
                            <div class="controls">
                                <input type="file" data-default-file="{{asset('storage/advisors/'.$users->id.'/'.$users->upload_qual_doc)}}" id="upload_qual_doc" name="upload_qual_doc" class="dropify" data-max-file-size="20M" data-max-height="2000" />
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
                                    <option {{($users->pos_income_id==$pos_income->id)?'selected':''}} value="{{$pos_income->id}}">{{$pos_income->title}}</option>
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
                                        <option {{($users->total_fn_yr==$i)?'selected':''}} value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">Months
                                    <select class=" form-control " name="total_fn_month" id="total_fn_month">
                                        @for($i=0; $i<=12; $i++)
                                        <option {{($users->total_fn_month==$i)?'selected':''}} value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="office_space">Do you have an office space to sell financial products?</label>
                            <div class="controls">
                                <div class="radio">
                                    <input {{($users->office_space=='y')?'checked':''}} type="radio" value="y" name="office_space" id="office_space" >
                                    <label for="office_space">Yes</label>
                                </div>
                                <div class="radio">
                                    <input {{($users->office_space=='n')?'checked':''}} type="radio" value="n" name="office_space" id="office_space2" >
                                    <label for="office_space2">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="did_sell">Which of these products did you sell?</label>
                        @php
                            $relFin=array();
                            foreach($rel_fins as $rel_fin){
                                $relFin[]=$rel_fin->fin_product_id;
                            }
                        @endphp
                            <div class="controls">
                                @foreach ($fin_products as $fin_product)
                                    <div class="checkbox">
                                        <input {{(in_array($fin_product->id, $relFin))?'checked':''}} type="checkbox" id="{{strtolower(str_replace(' ','_',$fin_product->product_name))}}" name="did_sell[]" value="{{$fin_product->id}}">
                                        <label for="{{strtolower(str_replace(' ','_',$fin_product->product_name))}}">{{$fin_product->product_name}}</label>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total_ex_yr">Have Agency or POS License?</label>
                            <div class="controls">
                                <div class="radio">
                                    <input {{($users->pos_licence=='y')?'checked':''}} type="radio" value="y" name="pos_licence" id="pos_licence" >
                                    <label for="pos_licence">Yes</label>
                                </div>
                                <div class="radio">
                                    <input {{($users->pos_licence=='n')?'checked':''}} type="radio" value="n" name="pos_licence" id="pos_licence2" >
                                    <label for="pos_licence2">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total_bus_anum">Total Finatial Business Income per anum</label>
                            <div class="controls">
                                <input type="text" value="{{$users->total_bus_anum}}" id="total_bus_anum" name="total_bus_anum" class=" form-control">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabsleft-tab5">
                        <div class="form-group">
                            @php
                                $add_proof = $users->address->add_proof??"";
                            @endphp
                            <label for="add_proof">Select Address Proof to be upload</label>
                            <div class="controls">
                                <select class=" form-control " name="add_proof" id="add_proof">
                                    <option value="">Select Address Proof</option>
                                    <option {{($add_proof=='p')?'selected':''}} value="p">Passport</option>
                                    <option {{($add_proof=='d')?'selected':''}} value="d">Driving Licence</option>
                                    <option {{($add_proof=='a')?'selected':''}} value="a">UID Aadhar</option>
                                    <option {{($add_proof=='v')?'selected':''}} value="v">Voter Id</option>
                                    <option {{($add_proof=='r')?'selected':''}} value="r">ration card</option>
                                    <option {{($add_proof=='o')?'selected':''}} value="o">others</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add_proof_no">Address Proof (ID) Number</label>
                            <div class="controls">
                                <input value="{{$users->address->add_proof_no??""}}" type="text" id="add_proof_no" name="add_proof_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="id_doc_front">Upload Address Proof Front and Back</label>
                            <div class="controls">@php
                                $id_doc_front = $users->address->id_doc_front??"";
                                $id_doc_back = $users->address->id_doc_back??"";
                            @endphp
                                <input type="file" data-default-file="{{asset('storage/advisors/'.$users->id.'/'.$id_doc_front)}}" id="id_doc_front" name="id_doc_front" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                            <div class="controls">
                                <input type="file" data-default-file="{{asset('storage/advisors/'.$users->id.'/'.$id_doc_back)}}" id="id_doc_back" name="id_doc_back" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pan_no">PAN Number</label>
                            <div class="controls">
                                <input type="text" value="{{$users->pan_no}}" id="pan_no" name="pan_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload_pan_no">Upload PAN Card</label>
                            <div class="controls">
                                <input type="file" data-default-file="{{asset('storage/advisors/'.$users->id.'/'.$users->upload_pan_no)}}" id="upload_pan_no" name="upload_pan_no" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gst_no">GST Number</label>
                            <div class="controls">
                                <input type="text" value="{{$users->gst_no}}" id="gst_no" name="gst_no" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload_gst_no">Upload GST Doc</label>
                            <div class="controls">
                                <input type="file" data-default-file="{{asset('storage/advisors/'.$users->id.'/'.$users->upload_gst_no)}}" id="upload_gst_no" name="upload_gst_no" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                    <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_status">User Status</label>
                            <div class="controls">
                                <select class="required form-control " name="user_status" id="user_status">
                                    <option value="">Select Status</option>
                                    <option {{($users->user_status=='1')?'selected':''}} value="1">Unverified</option>
                                    <option {{($users->user_status=='2')?'selected':''}} value="2">Verified</option>
                                </select>
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
{{-- <input type='text'
       value='99284'
       placeholder='Write your country name'
       class='flexdatalists'
       data-search-in='pincode'
       data-text-property='pincode'
       data-visible-properties='["id","pincode"]'
       data-value-property='id'
       data-min-length='1'
       name='country_preselect_id'> --}}
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

