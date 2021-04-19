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
        <h4 class="box-title">ADD PR BANNER</h4><hr>
        <div class="dropdown js__drop_down">
            <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
            <ul class="sub-menu">
                <li><a href="{{ route('crm-pr-banners') }}">All Banners</a></li>
                <li><a href="{{ route('pr-banner') }}">Add Banner</a></li>
            </ul>
        </div>

        <form id="bannerForm" action="{{ route('crm.store-pr-banners') }}" method="POST" enctype="multipart/form-data" autocomplete="off">

            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <div class="title">
                    <input type="text" name="title" class="form-control" id="title" placeholder="Title">
                </div>
            </div>

            {{-- 
                <div class="form-group">
                    <label for="card_img">Banner Image</label>
                    <div class="controls">
                        <input type="file" name="banner_img" id="banner_img" class="form-control" />
                    </div>
                </div> 
            --}}
    
            <div class="form-group">
                <label for="banner_img">Banner Image</label>
                <input type="file" id="banner_img" name="banner_img" class="dropify" data-default-file="http://placehold.it/1000x667" />
            </div>

            <hr>
            <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Add</button>
        </form>
    </div>

</div>

</div>

@endsection

