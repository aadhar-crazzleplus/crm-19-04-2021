@extends('crm.crmlayout')
@section('content')
<?php
    $arr = [];
    foreach ($permissions as $key => $permission) {
        $prArr = explode(",",$permission['permissions']);
        if(count($prArr) > 0) {
            $arr[$permission['user_type']][$permission['module']] = $prArr;
        }
    }

    // foreach ($crm_modules as $key => $value) {
    //     if (array_key_exists($key,$arr))
    //     {
    //         echo "<pre>";
    //         print_r($arr[$key]);
    //         echo "</pre>";
    //         echo "<br>";

    //         // if(in_array('Orange', $_POST['food'])){
    //         //     echo 'Orange was checked!';
    //         // }
    //     }
    // }
    // die;


    // echo "<pre>";
    // print_r($user_type);
    // die;

    // echo "<pre>"; print_r($arr); die;
?>
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
        <h4 class="box-title">Manage Permissions</h4><hr>

        <form id="permissionForm" action="{{ route('crm.store-permission') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            
            <div class="form-group margin-bottom-20">
                <label for="insurance">User Types</label>
                <select class="form-control" name="userType" id="userType">
                    <?php foreach ($user_type as $id => $userType) { ?>
                        <option value="<?= $id; ?>"><?= ucfirst($userType); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="box-content">
                <h4 class="box-title">Modules</h4>

                <div class="table-responsive">
                    <table class="table">
                        <thead> 
                            <tr> 
                                <th>Module</th> 
                                <th>All</th> 
                                <th>Read/View</th> 
                                <th>Add/Edit</th> 
                                <th>Delete</th> 
                            </tr> 
                        </thead> 
                        <tbody> 
                            <?php foreach ($crm_modules as $key => $value) { ?>
                                <?php //if (array_key_exists($key,$arr)) { ?>
                            <tr> 
                                <th scope="row"><?= ucfirst($value); ?></th>
                                <td>
                                    <div class="switch success">
                                        <input type="checkbox" class="<?= $value; ?>Checkbox" id="<?= $value; ?>-all" name="<?= $value; ?>[1]" value="1" />
                                        <label for="<?= $value; ?>-all"></label>
                                    </div>
                                </td> 
                                <td>
                                    <div class="switch success">
                                        <input type="checkbox" class="<?= $value; ?>Checkbox" id="<?= $value; ?>-read" name="<?= $value; ?>[2]" value="2" />
                                        <label for="<?= $value; ?>-read"></label>
                                    </div>
                                </td> 
                                <td>
                                    <div class="switch success">
                                        <input type="checkbox" class="<?= $value; ?>Checkbox" id="<?= $value; ?>-edit" name="<?= $value; ?>[3]" value="3" />
                                        <label for="<?= $value; ?>-edit"></label>
                                    </div>
                                </td> 
                                <td>
                                    <div class="switch success">
                                        <input type="checkbox" class="<?= $value; ?>Checkbox" id="<?= $value; ?>-delete" name="<?= $value; ?>[4]" value="4" />
                                        <label for="<?= $value; ?>-delete"></label>
                                    </div>
                                </td> 
                            </tr>
                                <?php //} ?>
                            <?php } ?>
                        </tbody> 
                    </table> 
                </div>
            </div>
			
            <hr>
            <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Updates</button>
        </form>
    </div>

</div>

</div>
<script src="">
    // $(document).ready(function () {
    //     $('#userType').change(function (e) { 
    //         e.preventDefault();
            
    //     });
    // });
</script>
@endsection
