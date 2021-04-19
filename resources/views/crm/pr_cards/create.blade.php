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
                <div class="alert alert-success">
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
        <h4 class="box-title">ADD PR CARD</h4><hr>
        
        <div class="dropdown js__drop_down">
            <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
            <ul class="sub-menu">
                <li><a href="{{route('crm.users')}}">All Cards</a></li>
                    <li><a href="{{route('crm.add-user')}}">Add Pr Card</a></li>
                    <li><a href="{{route('crm.deleted')}}">Deleted Cards</a></li>
            </ul>
        </div>

        <form id="commentForm" action="{{ route('crm.store-pr-cards') }}" method="POST" enctype="multipart/form-data" autocomplete="off">

            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <div class="title">
                    <input type="text" name="title" class="form-control" id="title" placeholder="Title">
                </div>
            </div>

            <div class="form-group">
                <label for="card_img">Card Image</label>
                <div class="controls">
                    <input type="file" name="card_img" id="card_img" class="form-control" />
                </div>
            </div>

            <div class="form-group">
                <label for="user_type">Categories</label>
                <div class="controls">
                    <select class="required form-control select2_2" name="categories[]" id="categories" multiple="multiple">
                        @foreach ($prCardCategory as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Submit</button>
        </form>
    </div>
    {{-- <input type="hidden" name="temp" id="temps" > --}}
    <!-- /.box-content -->
</div>
<!-- /.col-md-6 col-xs-12 -->
</div>
<script>

$(document).ready(function () {
    $('.select2_2').select2({});

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

