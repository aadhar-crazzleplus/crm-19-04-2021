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
        <h4 class="box-title">Sample Excels</h4>
        <div class="card-content">
            <ul class="list-inline margin-bottom-0">
                <form action="{{ route('crm.sampleFileDownload') }}" method="POST" id="sampleFileDownloadForm">
                    @csrf
                    <input type="hidden" name="file" id="js-file" class="js-file" />
                    <button type="button" class="btn btn-icon btn-icon-right btn-primary btn-xs waves-effect waves-light" onclick="downloadFile(1);"><i class="ico fa fa-download"></i>Used Vehicle Loan</button>
                    <button type="button" class="btn btn-icon btn-icon-right btn-info btn-xs waves-effect waves-light" onclick="downloadFile(2);"><i class="ico fa fa-download"></i>Credit Card Request</button>
                    <button type="button" class="btn btn-icon btn-icon-right btn-danger btn-xs waves-effect waves-light" onclick="downloadFile(3);"><i class="ico fa fa-download"></i>Insurance sheet mis</button>
                    <button type="button" class="btn btn-icon btn-icon-right btn-warning btn-xs waves-effect waves-light" onclick="downloadFile(4);"><i class="ico fa fa-download"></i>Term Insurance Sheet</button>
                </form>
            </ul>
        </div>
    </div>

    <div class="box-content card white">
        <h4 class="box-title">Chose File To Upload</h4>
        <div class="card-content">
            <div class="radio">
                <input type="radio" name="payout_file" id="used_vehicle_loan" value="1" checked /><label for="used_vehicle_loan">Used Vehicle Loan MIS</label>
                &nbsp;&nbsp;
                <input type="radio" name="payout_file" id="credit_card_request" value="2" /><label for="credit_card_request">Credit Card Request (Responses)</label>
                &nbsp;&nbsp;
                <input type="radio" name="payout_file" id="insurance_sheet_mis" value="3" /><label for="insurance_sheet_mis">Insurance Sheet MIS</label>
                &nbsp;&nbsp;
                <input type="radio" name="payout_file" id="term_insurance" value="4" /><label for="term_insurance">Term Insurance Sheet</label>
                &nbsp;&nbsp;
                {{-- <input type="radio" name="payout_file" id="compair_excel" value="5" /><label for="compair_excel">Compair Excel Sheet</label> --}}
            </div>
        </div>
    </div>

    <div class="box-content">
        <form id="payoutImportForm" action="{{ route('crm.store-payout-file') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <input type="hidden" name="file_type" class="js-file-type" id="js-file-type" value="1" />
            <div class="form-group">
                <label for="payout_file">Payout File</label>
                <input type="file" id="payout_file" name="payout_file" class="dropify" data-default-file="http://placehold.it/1000x200" />
            </div>
            <hr>
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="importFile();">Import</button>
        </form>
    </div>

</div>

</div>

@endsection

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    function downloadFile(value) {
        $('#js-file').val(value);
        $('#sampleFileDownloadForm').submit();
    }

    function importFile() {
        var payout_file = $("input[type='radio'][name='payout_file']:checked").val();
        // console.info(payout_file);
        // return false;
        $('#js-file-type').val(payout_file);
        $('#payoutImportForm').submit();
    }
</script>