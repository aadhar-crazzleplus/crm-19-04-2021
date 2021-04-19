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
        <h4 class="box-title">Change Password</h4>
        <!-- /.box-title -->
        <!-- /.dropdown js__dropdown -->
        <form id="commentForm" data-toggle="validator" action="{{ route('crm.changepass') }}" method="POST" autocomplete="off">

            @csrf
            <div id="tabsleft" class="tabbable tabs-left">
                <ul>
                    <li><a href="#tabsleft-tab1" data-toggle="tab">Change Password</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tabsleft-tab1">

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="controls">
                                <input type="password" id="current_password" name="current_password" placeholder="Current Password" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="controls">
                                <input type="password" data-minlength="6" id="new_password" name="new_password" placeholder="New Password" class="form-control" required>
                                <div class="help-block">Minimum of 6 characters</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Confirm Password</label>
                            <div class="controls">
                                <input type="password" id="new_confirm_password" name="new_confirm_password" data-match="#new_password" data-match-error="Whoops, these don't match" placeholder="Confirm Password" class="form-control" required>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>


                    </div>

                    <ul class="pager wizard">
                        <li class="finish"><a href="javascript:;">Change Password</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
    <!-- /.box-content -->
</div>
<!-- /.col-md-6 col-xs-12 -->
</div>

@endsection

