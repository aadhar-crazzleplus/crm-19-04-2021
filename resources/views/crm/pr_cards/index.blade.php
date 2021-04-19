@extends('crm.crmlayout')

@section('content')
<div class="row">

<?php
    $cats = [];
    foreach ($prCardImage as $key => $value) {
        $cats[$value->id] = $value->pivotCategory->pluck('pr_card_category_id');
    }

    $imgCat = json_decode(json_encode($cats), true);

    $year = date("Y");
    $month = date("M");
    $path = 'pr_cards/'.$year.'/'.$month.'/';
?>

<div class="col-md-12 col-xs-12 col-lg-12">
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
        <h4 class="box-title"></h4>
        <!-- /.box-title -->
        <div class="dropdown js__drop_down">
            <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
            <ul class="sub-menu">
                <li><a href="{{route('crm.users')}}">All Cards</a></li>
                    <li><a href="{{route('crm.add-user')}}">Add Pr Card</a></li>
                    <li><a href="{{route('crm.deleted')}}">Deleted Cards</a></li>
            </ul>
            <!-- /.sub-menu -->
        </div>
        <div class="isotope-filter js__filter_isotope isotope">
			<ul class="filter-controls">
                <li><a href="#" class="js__filter_control js__active" data-filter="*">All</a></li>
                <?php foreach ($prCardCategory as $key => $value) { ?>
                    <li><a href="#" class="js__filter_control" data-filter=".<?= $value->id; ?>"><?= ucwords($value->name); ?></a></li>
                <?php } ?>
			</ul>
			<hr>
			<div class="row row-inline-block small-spacing js__isotope_items">
                <?php 
                foreach ($prCardImage as $key => $value) { 
                    $class=implode(" ",$imgCat[$value->id]);
                ?>
                    <div class="col-md-4 col-sm-6 col-tb-6 col-xs-12 js__isotope_item <?= $class;?>" >
                        <a href="<?php echo $value->name; ?>" class="item-gallery lightview <?php echo $value->id; ?>" data-lightview-group="group">
                            <img src="<?php echo $value->name; ?>" alt="<?php echo $value->id; ?>" >
                            <h2 class="title"> <?php echo $value->title; ?></h2>
                        </a>
                    </div>
                <?php 
                } 
                ?>
			</div>
            <hr>
		</div>
    </div>
    {{-- <input type="hidden" name="temp" id="temps" > --}}
    <!-- /.box-content -->
</div>
<!-- /.col-md-6 col-xs-12 -->
</div>
<script>
$(document).ready(function () {
    $('.select2_2').select2({});

    // Isotope filter
    // $('.js__isotope_items').isotope({ filter: '.1' })
});
</script>

@endsection

