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
        <h4 class="box-title">Database Analytics</h4><hr>
        
        <div class="box-content">
            <form id="compareDatabaseForm" action="{{ route('crm.comparedatabases') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="form-group">
                    <label for="sheets">Enter Another Database Name</label>
                    <input type="text" name="database" class="form-control" id="database" placeholder="Database" required="required" />
                </div>
                <hr>
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="importFile();">Generate Results</button>
            </form>
        </div>

    </div>



</div>

</div>

@endsection


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    function importFile() {
        $('#compareDatabaseForm').submit();
    }
</script>