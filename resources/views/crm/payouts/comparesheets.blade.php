@extends('crm.crmlayout')
@section('content')
<style>
    .dropify-wrapper {
        height: 130px;
    }
</style>
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

    @if ($message = Session::get('error'))
        <div class="alert alert-danger" style="border: 2px solid #CE9694;">
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

    <div class="box-content card white">
        <h4 class="box-title text-center text-muted">COMPARE EXCELS</h4>
    </div>

    <div class="box-content">
        <form id="compareSheetsForm" action="{{ route('crm.comparesheets') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            {{-- <input type="hidden" name="file_type" class="js-file-type" id="js-file-type" value="1" /> --}}
            <div class="form-group">
                <label for="sheets">Excel Sheets</label>
                <input type="file" id="sheets" name="sheets[]" class="dropify" data-default-file="http://placehold.it/1000x200" multiple="multiple" />
            </div>
            <hr>
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="importFile();">Generate Results</button>
        </form>
    </div>

</div>

</div>

@endsection

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    // function downloadFile(value) {
    //     $('#js-file').val(value);
    //     $('#sampleFileDownloadForm').submit();
    // }

    function importFile() {
        // var payout_file = $("input[type='radio'][name='payout_file']:checked").val();
        // console.info(payout_file);
        // return false;
        // $('#js-file-type').val(payout_file);
        $('#compareSheetsForm').submit();
    }
</script>