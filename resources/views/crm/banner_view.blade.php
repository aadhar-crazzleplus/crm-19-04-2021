@extends('crm.crmlayout')

@section('content')

<style>
    .css-box-content {
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.19);
    }
</style>




<div class="row small-spacing">
    <div class="col-xs-12">
        <div class="box-content css-box-content">
            <h4 class="box-title">Banner</h4>
            <!-- /.box-title -->
            <div class="dropdown js__drop_down">
                <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
                <ul class="sub-menu">
                    <li><a href="{{route('banner-view')}}">All</a></li>
                    <li><a data-toggle="modal" data-target="#myModal1" href="#myModal1">Add News</a></li>
                    {{-- <li><a href="{{route('social-deleted')}}">Deleted List</a></li> --}}
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
                        <th>Title</th>
                        <th>Status</th>
                        <th>Updated by</th>
                        <th>Updated at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Updated by</th>
                        <th>Updated at</th>
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
        ajax: "{{route('banner-list')}}",
        columns: [
        { data: 'title' },
        { data: 'status' },
        { data: 'updated_by' },
        { data: 'updated_at'},
        { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[ 3, "desc" ]],
        dom: 'lBfrtip',
        buttons: [
            { extend: 'colvis', columns: ':not(:first-child)' },
            { text: 'Add New', action: function ( e, dt, node, config ) { $('#myModal1').modal('show'); } },
        ],
    });

    $(".vhclose").on("click", function(){
        table.ajax.reload();
        $('#form1')[0].reset();
    });
    $(".edit_vhclose").on("click", function(){
        table.ajax.reload();
        $('#edit_form1')[0].reset();
    });

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "rtl": false,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": 300,
        "hideDuration": 1000,
        "timeOut": 10000,
        "extendedTimeOut": 1000,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    $("div[id^='myModal']").each(function(){

        var currentModal = $(this);
        $(".processing").hide();
        $('#image').dropify();

        //click next
        currentModal.find('.btn-next').click(function(){
            $(".regu").hide();
            $(".processing").show();
            var froms = $(this).data("form");
            var formId = $('#'+froms)[0];
            var fd = new FormData(formId);
            $.ajax({
                url: $(this).data("route"),
                processData: false,
                contentType: false,
                method: 'post',
                data: fd,
                success: function(result) {
                    $(".regu").show();
                    $(".processing").hide();
                    // console.log('Error:', result);
                    if(result.success) {
                        toastr["success"]("Performed !");
                        currentModal.modal('hide');
                        currentModal.closest("div[id^='myModal']").nextAll("div[id^='myModal']").first().modal('show');
                        table.ajax.reload();
                        $('#form1')[0].reset();
                    } else {
                        var err = '';
                        $.each(result.data, function(key, value) {
                            err += value+"<br>";
                            $('#'+key).addClass('has-error');
                        });
                        toastr["error"](err);
                    }
                },
                error: function (data) {
                    $(".regu").show();
                    $(".processing").hide();
                    var err = '';
                        $.each(data.responseJSON.data, function(key, value) {
                            err += value+"<br>";
                        });
                        toastr["error"](err);
                }
            });
        });

        //click prev
        currentModal.find('.btn-prev').click(function(){
            currentModal.modal('hide');
            currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().modal('show');
        });

    });


    $("div[id^='editModal']").each(function(){

    var currentEditModal = $(this);
    $(".processing").hide();

    //click next
    currentEditModal.find('.btn-next').click(function(){
        $(".regu").hide();
        $(".processing").show();
        var froms = $(this).data("form");
        var formId = $('#'+froms)[0];
        var fd = new FormData(formId);
        $.ajax({
            url: $(this).data("route"),
            processData: false,
            contentType: false,
            method: 'post',
            data: fd,
            success: function(result) {
                $(".regu").show();
                $(".processing").hide();
                // console.log('Error:', result);
                if(result.success) {
                    toastr["success"]("Performed !");
                    currentEditModal.modal('hide');
                    currentEditModal.closest("div[id^='editModal']").nextAll("div[id^='editModal']").first().modal('show');

                    table.ajax.reload();
                    $('#edit_form1')[0].reset();
                    // $('.dropify-clear').click();

                    // var drEvent = $('#edit_image').dropify();
                    // drEvent = drEvent.data('dropify');
                    // drEvent.resetPreview();
                    // drEvent.clearElement();
                    // drEvent.settings.defaultFile = publicpath_edit_image;
                    // drEvent.destroy();
                    // drEvent.init();
                    // $('.dropify#edit_image').dropify({
                    // defaultFile: publicpath_edit_image,
                    // });

                } else {
                    var err = '';
                    $.each(result.data, function(key, value) {
                        err += value+"<br>";
                        $('#'+key).addClass('has-error');
                    });
                    toastr["error"](err);
                }
            },
            error: function (data) {
                $(".regu").show();
                $(".processing").hide();
                var err = '';
                    $.each(data.responseJSON.data, function(key, value) {
                        err += value+"<br>";
                    });
                    toastr["error"](err);
            }
        });
    });

        //click prev
        currentEditModal.find('.btn-prev').click(function(){
            currentEditModal.modal('hide');
            currentEditModal.closest("div[id^='editModal']").prevAll("div[id^='editModal']").first().modal('show');
        });

    });


    $('body').on('shown.bs.modal', '.modal', function() {
    $(this).find('select').each(function() {
        var dropdownParent = $(document.body);
        if ($(this).parents('.modal.in:first').length !== 0)
        dropdownParent = $(this).parents('.modal.in:first');

        $(".select2_1").select2({
            dropdownParent: dropdownParent
        });

    });
    });

});

function editBanner(edit_route){
    $.ajax({
        url:edit_route,
        type: "GET",
        // data: {
        //     _token: '{{csrf_token()}}'
        // },
        dataType : 'json',
        success: function(result){
            $("#edit_id").val(result.data.id);
            $("#edit_title").val(result.data.title);
            $("#edit_status").val(result.data.status).trigger('change');
            $("#edit_url").val(result.data.url);
            $("#edit_image").attr('data-default-file', result.data.image);
            $('.dropify').dropify();
        }
    });
}

function showBanner(id){
    $.ajax({
        url:"{{route('banner-show')}}",
        type: "POST",
        data: {
            id:id,
            _token: '{{csrf_token()}}'
        },
        dataType : 'json',
        success: function(result){
            $("#show_title").html(result.data.title);
            $("#show_status").html(result.data.status);
            $("#show_image").attr("src", result.data.image);
            $("#show_updated_by").html(result.data.updated_by);
            $("#show_url").html(result.data.url);
        }
    });
}

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
                title:"Deleted successfully",
                text:"See you later!",
                type:"success",
                confirmButtonColor:"#304ffe"
            },
            function() {
                window.location.href = val;
            });
        } else {
            swal("Cancelled", "All right !! Record is still safe :)", "error");
        }
        });
    }
</script>
@endsection

@section('addmodals')
{{-- Modals --}}
<!-- Button trigger add modal -->
    <!-- add Modal -->
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="myModalLabel">Add Promotional Banner</h4>
              </div>
              <div class="modal-body">
                 <form role="form" id="form1" action="" method="post" enctype="multipart/form-data" class="registration-form">
                    <fieldset style="display: block;">
                       <div class="form-top">
                          <div class="form-top-left">
                             {{-- <h3>Step 1 / 1</h3>
                             <p>Tell us basic info:</p> --}}
                          </div>

                       </div>
                       @csrf
                       <div class="form-bottom">
                          <div class="form-group">
                             {{-- <label class="sr-only" for="mobile_no">Mobile Number</label> --}}
                             <label for="title">Title</label>
                             <div class="controls">
                                <input name="title" placeholder="Title..." class="form-control input-error" id="title" type="text">
                             </div>
                          </div>
                          <div class="form-group">
                            {{-- <label class="sr-only" for="mobile_no">Mobile Number</label> --}}
                            <label for="url">URL</label>
                            <div class="controls">
                               <input name="url" placeholder="URL..." class="form-control input-error" id="url" type="text">
                            </div>
                         </div>
                          <div class="form-group">
                             <label for="image">Upload Image</label>
                             <div class="controls">
                                <input type="file" id="image" name="image" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                                <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                            </div>
                          </div>

                       </div>
                    </fieldset>

                 </form>
              </div>
              <div class="modal-footer">
                 {{-- <button type="button" class="btn btn-default btn-prev">Prev</button> --}}
                 <button type="button" id="next1" data-form="form1" data-route="{{ route('banner-add') }}" class="btn btn-default regu btn-next">Submit</button>
                 {{-- <button type="button" class="btn btn-default vhclose" data-dismiss="modal">Close</button> --}}
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- Edit Modal -->
     <div class="modal fade" id="editModal1" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog  modal-dialog-scrollable" role="document" style="position: relative; overflow-y: auto; height:600px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close edit_vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="editModalLabel">Banner Update</h4>
              </div>
              <div class="modal-body">
                <form id="edit_form1" action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="edit_id" value="" />
                 <fieldset>
                       <div class="form-top">
                          <div class="form-top-left">
                             {{-- <h3>Step 1 / 1</h3> --}}
                             {{-- <p>Check vehicle details:</p> --}}
                          </div>

                       </div>
                       <div class="form-bottom">

                        <div class="form-group">
                           {{-- <label class="sr-only" for="mobile_no">Mobile Number</label> --}}
                           <label for="title">Title</label>
                           <div class="controls">
                              <input name="title" placeholder="Title..." class="form-control input-error" id="edit_title" type="text">
                           </div>
                        </div>
                        <div class="form-group">
                           {{-- <label class="sr-only" for="mobile_no">Mobile Number</label> --}}
                           <label for="url">URL</label>
                           <div class="controls">
                              <input name="url" placeholder="URL..." class="form-control input-error" id="edit_url" type="text">
                           </div>
                        </div>
                        <div class="form-group">
                           <label for="image">Upload Image</label>
                           <div class="controls">
                              <input type="file" id="edit_image" name="image" class="dropify" data-max-file-size="20M" data-max-height="2000" />
                              <p class="help margin-top-10">Only portrait or square images, 20M max and 2000px max-height.</p>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="cat_id">Status</label>
                            <div class="controls">
                               <select class=" form-control occupation_id select2_1" name="status" id="edit_status">
                                   <option value="1">Active</option>
                                   <option value="0">InActive</option>
                               </select>
                            </div>
                         </div>
                     </div>
                    </fieldset>
                </form>
              </div>
              <div class="modal-footer">
                 {{-- <button type="button" id="edit_pre1" class="btn btn-default btn-prev">Prev</button> --}}
                 <button type="button" id="edit_next1" data-form="edit_form1" data-route="{{ route('banner-update') }}" class="btn btn-default regu btn-next">Submit</button>
                 {{-- <button type="button" class="btn btn-default vhclose" data-dismiss="modal">Close</button> --}}
                 <button type="button" class="btn btn-default processing" data-dismiss="modal">Processing please wait.....</button>
              </div>
           </div>
        </div>
     </div>
     <!-- Button trigger modal -->

     <!-- Show lead Modal -->
     <div class="modal fade bd-example-modal-lg" id="showModal1" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="position: relative; overflow-y: auto; height:600px;">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" class="close vhclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title" id="showModalLabel">Show Banner Details: <span id="show_product_title"></span></h4>
              </div>
              <div class="modal-body">

                <div class="box-content card">
                    <h4 class="box-title"><i class="fa fa-envira ico"></i>Promotional Banner Details</h4>

                    <div class="card-content">
                        <div class="row">

                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Title:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_title"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Status:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_status"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Updated By:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_updated_by"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>URL:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7" id="show_url"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <div class="col-md-6 list-group-item">
                                <div class="row">
                                    <div class="col-xs-5"><label>Image:</label></div>
                                    <!-- /.col-xs-5 -->
                                    <div class="col-xs-7"><img src="" id="show_image"></div>
                                    <!-- /.col-xs-7 -->
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.col-md-6 -->

                        </div>
                    </div>
                    <!-- /.card-content -->
                </div>

              </div>
              <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
           </div>
        </div>
     </div>
@endsection
