@extends('crm.crmlayout')
<?php 
    $notificationStatus = Config::get('constants.notifiction_status');
?>


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

        <form id="notificationForm" action="{{ route('crm.store-notification') }}" method="POST" enctype="multipart/form-data" autocomplete="off">

            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <div class="title">
                    <input type="text" name="title" class="form-control" id="title" placeholder="Title">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <div class="description">
                    <textarea class="form-control" id="description" placeholder="Description" maxlength="225" name="descrption"></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="form-group margin-bottom-20">
                    <label for="status">Status</label>
                    <select class="form-control" name="status" id="status">
                        <?php foreach ($notificationStatus as $id => $val) { ?>
                            <option value="<?php echo $id; ?>"><?php echo ucfirst($val); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
   
            <hr>
            <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Add</button>
        
        </form>

    </div>

</div>

</div>

@endsection

