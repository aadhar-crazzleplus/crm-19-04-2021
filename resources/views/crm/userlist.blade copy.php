@extends('crm.crmlayout')

@section('content')

<div class="row small-spacing">
    <div class="col-xs-12">
        <div class="box-content">
            <h4 class="box-title">Default</h4>
            <!-- /.box-title -->
            <div class="dropdown js__drop_down">
                <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
                <ul class="sub-menu">
                    <li><a href="{{route('crm.users')}}">All Users</a></li>
                    <li><a href="{{route('crm.add-user')}}">Add User</a></li>
                    <li><a href="{{route('crm.deleted')}}">Deleted Users</a></li>
                    <li class="split"></li>
                </ul>
                <!-- /.sub-menu -->
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            <!-- /.dropdown js__dropdown -->
            <div id="containers" class="small-6 columns"></div>
            <table id="usrTable" class="table table-striped table-bordered display" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile No</th>
                        <th>User Code</th>
                        <th>User Type</th>
                        <th>Pincode</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Mobile No</th>
                        <th>User Code</th>
                        <th>User Type</th>
                        <th>Pincode</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-content -->
    </div>
    <!-- /.col-xs-12 -->

</div>
<script>

$(document).ready(function () {

// DataTable
var table = $('#usrTable').DataTable({
        processing: true,
        serverSide: true,

        ajax: "{{route('crm.getusers')}}",
        columns: [
        { data: 'first_name' },
        { data: 'mobile_no' },
        { data: 'user_code' },
        { data: 'user_type' },
        { data: 'pincode', orderable: false, searchable: false },
        { data: 'user_status' },
        { data: 'created_at'},
        { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[ 6, "desc" ]],
        dom: 'lBfrtip',
        buttons: [
            { extend: 'colvis', columns: ':not(:first-child)' },'excel', 'pdf', 'print',
            { text: 'Add New', action: function ( e, dt, node, config ) { window.location="{{ route('crm.add-user') }}"; } },
        ],

    });

});
function deleete(val){
        swal({
            title: "Delete?",
            text: "Are you sure you want to Delete?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor:"#f60e0e",
            confirmButtonText:"Yes, I'm!",
            cancelButtonText:"No, Pressed Mistake!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
        if (isConfirm) {
            swal({
                title:"User deleted successfully",
                text:"See you later!",
                type:"success",
                confirmButtonColor:"#304ffe"
            },
            function() {
                window.location.href = val;
            });
        } else {
            swal("Cancelled", "All right !! User is still safe :)", "error");
        }
        });
    }
</script>
@endsection

