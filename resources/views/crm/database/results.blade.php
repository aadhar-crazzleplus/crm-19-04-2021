@extends('crm.crmlayout')
@section('content')
<?php 
    // echo "<pre>";
    // print_r($dbOneTables);
    // die('gsahfdghf');
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
      <div class="col-lg-6" style="padding-top: 1%;">
        <h4 class="box-title pull-left">DATABASE ANALYTICS</h4>
      </div>
      <div class="col-lg-6">
        <a href="{{ route('crm-database') }}" class="pull-right">
          <button type="button" class="btn btn-icon btn-icon-left btn-xs btn-violet waves-effect waves-light">
            <i class="ico fa fa-arrow-left"></i>
            Compare Again
          </button>
        </a>
      </div>
        
        
        <hr>
        
        <div id="donutchart" style="width: 900px; height: 500px;"></div>

    </div>


    <div class="box-content card white">
      <div class="col-lg-3">
    
        <table class="table table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">DATABASE-1 TABLES</th>
            </tr>
          </thead>

          <tbody>
            <?php 

                if(isset($firstDatabasealltables)) {
                  $i = 1;
                  foreach ($firstDatabasealltables as $table) {
            ?>
            <tr>
              <th scope="row"><?php echo $i; ?></th>
              <td><?php echo $table; ?></td>
            </tr>
            <?php
                  $i++;
                  }
                }
            ?>
          </tbody>
        </table>

      </div>

      <div class="col-lg-3">
        
        <table class="table table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">DATABASE-2 TABLES</th>
            </tr>
          </thead>

          <tbody>
            <?php 
                if(isset($secondDatabasealltables)) {
                  $j = 1;
                  foreach ($secondDatabasealltables as $tble) {
            ?>
            <tr>
              <th scope="row"><?php echo $j; ?></th>
              <td><?php echo $tble; ?></td>
            </tr>
            <?php
                  $j++;
                  }
                }
            ?>
          </tbody>
        </table>
        
      </div>


      <div class="col-lg-3">
        
        <table class="table table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Intersection</th>
            </tr>
          </thead>

          <tbody>
            <?php 
                if(isset($cdata['intersect'])) {
                  $b = 1;
                  foreach ($cdata['intersect'] as $intersect) {
            ?>
            <tr>
              <th scope="row"><?php echo $b; ?></th>
              <td style="background-color: rgb(236, 248, 234);"><?php echo $intersect; ?></td>
            </tr>
            <?php
                  $b++;
                  }
                }
            ?>
          </tbody>
        </table>
        
      </div>
      
      <div class="col-lg-3">
        
        <table class="table table-hover">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Differences</th>
            </tr>
          </thead>

          <tbody>
            <?php 
                if(isset($cdata['diff'])) {
                  $a = 1;
                  foreach ($cdata['diff'] as $diff) {
            ?>
            <tr>
              <th scope="row"><?php echo $a; ?></th>
              <td style="background-color: #fff6f5;"><?php echo $diff; ?></td>
            </tr>
            <?php
                  $a++;
                  }
                }
            ?>
          </tbody>
        </table>
        
      </div>

    </div>


</div>

</div>

@endsection


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load("current", {packages:["corechart"]});
  google.charts.setOnLoadCallback(drawChart);
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Statics', 'Total Numbers'],
      ['DATABASE-1',  <?php echo $noOfTableDatabaseOne; ?>],
      ['DATABASE-2',  <?php echo $noOfTableDatabaseTwo; ?>],
      ['INTERSECTIONS',  <?php echo count($cdata['intersect']); ?>],
      ['DIFFERENCES',  <?php echo count($cdata['diff']); ?>],
    ]);

    var options = {
      title: 'DATABASE STATICS',
      pieHole: 0.4,
    };

    var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
    chart.draw(data, options);
  }
</script>