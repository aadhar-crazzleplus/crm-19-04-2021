@extends('crm.crmlayout')

@section('content')
<style>
figure {
    border-left: 4px rgba(200,200,200,0.9) solid;
    border-bottom: 1px solid rgba(200,200,200,0.9);
    background-color: rgba(220,220, 220, 0.5);
    padding: 6px;
    margin: auto;
}

figcaption {
    text-decoration: underline;
    color: rgba(50,50,50,0.8);
    font-style: italic;
    padding: 4px;
    text-align: left;
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
            <h4 class="box-title">PR BANNERS</h4>
            
            <div class="dropdown js__drop_down">
                <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
                <ul class="sub-menu">
                    <li><a href="{{ route('crm-pr-banners') }}">All Banners</a></li>
                    <li><a href="{{ route('pr-banner') }}">Add Banner</a></li>
                </ul>
                <!-- /.sub-menu -->
            </div>
            
            <div class="row">
                <?php foreach ($banners as $banner) { ?>
                <div class="col-md-4">
                    <figure>
                        <img src="<?= $banner->image; ?>" alt="Img" style="width:100%">
                        <figcaption><?= ucfirst($banner->title); ?></figcaption>
                    </figure>

                    <?php /* ?>
                    <div class="thumbnail">
                    <a href="<?= $banner->image; ?>"  target="_blank">
                        <img src="<?= $banner->image; ?>" alt="Image" style="width:100%" />
                        <div class="caption text-center alert" style="background-color: rgba(220,220,220,0.4);">
                            <p><?= strtoupper($banner->title); ?></p>
                        </div>
                    </a>
                    </div>
                    <?php */ ?>

                </div>
                <?php } ?>
              
            </div>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Isotope filter
    // $('.js__isotope_items').isotope({ filter: '.1' })
});
</script>

@endsection

