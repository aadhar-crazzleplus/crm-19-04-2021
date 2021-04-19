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
                    <li><a href="{{route('admins')}}">All Admin Users</a></li>
                    <li><a href="{{route('add-admin')}}">Add Admin User</a></li>
                    <li><a href="{{route('deleted-admin')}}">Deleted Admin Users</a></li>
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
            <table id="usrTable" class="table table-striped table-bordered display" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>User Code</th>
                        <th>User Type</th>
                        <th>Pincode</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>User Code</th>
                        <th>User Type</th>
                        <th>Pincode</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                {{-- <tbody>
                @foreach ($users as $user)

                @php
                    if($user->user_status<1){
                    $status = "Deleted";
                    $class="text-danger";
                    }else if ($user->user_status == '1'){
                        $status = "Unverified";
                        $class="text-warning";
                    }else if ($user->user_status == '2'){
                    $status = "Verified";
                    $class="text-success";
                }
                @endphp
                    <tr>
                        <td>{{ucfirst($user->first_name.' '.$user->last_name)}}</td>
                        <td>{{$user->user_code}}</td>
                        <td>{{ucfirst($user->user_type)}}</td>
                        <td>{{$user->address->pincode->pincode??""}}</td>
                        <td class="{{$class}}">{{$status}}</td>
                        <td><a href="{{route('crm.edit-user',$user->id)}}">Edit</a> | <a href="javascript:;" onclick="deleete('{{ route('crm.delete-user',$user->id) }}');">Delete</a></td>
                    </tr>
                @endforeach
                </tbody> --}}
            </table>
        </div>
        <!-- /.box-content -->
    </div>
    <!-- /.col-xs-12 -->

</div>
<script>

$(document).ready(function () {
    $('#usrTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{route('getdeleted-admin')}}",
        columns: [
        { data: 'first_name' },
        { data: 'user_code' },
        { data: 'user_type' },
        { data: 'pincode', orderable: false, searchable: false },
        { data: 'user_status' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
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
            swal("Confirmed", "All right !! User is still safe, next time please :)", "error");
            // swal({
            //     title:"User deleted successfully",
            //     text:"See you later!",
            //     type:"success",
            //     confirmButtonColor:"#304ffe"
            // },
            // function() {
            //     window.location.href = val;
            // });
        } else {
            swal("Cancelled", "All right !! User is still safe :)", "error");
        }
        });
    }
</script>
@endsection
