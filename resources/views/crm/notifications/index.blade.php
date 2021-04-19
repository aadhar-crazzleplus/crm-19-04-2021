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
            <h4 class="box-title">NOTIFICATION</h4>
            
            <div class="dropdown js__drop_down">
                <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
                <ul class="sub-menu">
                    <li><a href="{{ route('crm-pr-banners') }}">All Banners</a></li>
                    <li><a href="{{ route('pr-banner') }}">Add Banner</a></li>
                </ul>
                <!-- /.sub-menu -->
            </div>
            
            <div class="row">
                    
            </div>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Isotope filter
    // $('.js__isotope_items').isotope({ filter: '.1' })
});
</script>

@endsection

