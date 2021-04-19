@extends('crm.crmlayout')
@section('content')
<style>
    .dropify-wrapper {
        height: 130px;
    }

    .css-heading {
        color: rgba(122,122,122, 1)
    }

    .css-h4 {
        padding: 2%;
        background-color: rgba(153,153,153, 0.3);
        border-left: 4px solid rgba(153,153,153, 0.9);
    }

    .css-no-data {
      border: 2px solid rgba(100, 100, 100, 0.7);
      color: rgba(100, 100, 100, 0.9);
      background-color: rgba(220, 220, 220, 0.4);
      max-width: 300px;
      margin: 1% auto;
      padding: 4%;
      border-radius: 4px;
    }
    
</style>

<div class="row">
  <?php if ((count($cdata['diff'])!=0) && (count($cdata['intersect'])!=0)) { ?>
  <div class="col-md-12 col-xs-12 col-lg-12">

      <div class="row css-analytics">
        
        

        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="box-content bg-success text-white">
            <div class="statistics-box with-icon">
              <i class="ico small fa fa-gg"></i>
              <p class="text text-white">Differences</p>
              <h2 class="counter"><?php echo count($cdata['diff']); ?></h2>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="box-content bg-info text-white">
            <div class="statistics-box with-icon">
              <i class="ico small fa fa-link"></i>
              <p class="text text-white">Intersections</p>
              <h2 class="counter"><?php echo count($cdata['intersect']); ?></h2>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <a href="{{ route('crm.comparesheets') }}">
            <button type="button" class="pull-right btn btn-icon btn-icon-left btn-xs btn-violet waves-effect waves-light">
              <i class="ico fa fa-arrow-left"></i>
              Compare Again
            </button>
          </a>
        </div>
      </div>

      
      <div id="donutchart" style="width: 900px; height: 500px;"></div>
    
      


      <div class="box-content card white">
        <div class="col-lg-6">
        <?php 
        if(isset($cdata['diff']) && count($cdata['diff']) > 0) {
        ?>
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
                    $i = 1;
                    foreach ($cdata['diff'] as $diff) {
              ?>
              <tr>
                <th scope="row"><?php echo $i; ?></th>
                <td><?php echo $diff; ?></td>
              </tr>
              <?php
                    $i++;
                    }
                  }
              ?>
            </tbody>
          </table>
          <?php
            }
          ?>
        </div>

        <div class="col-lg-6">
          <?php 
            if(isset($cdata['intersect']) && count($cdata['intersect']) > 0) {
          ?>
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Intersections</th>
              </tr>
            </thead>

            <tbody>
              <?php 
                  if(isset($cdata['intersect'])) {
                    $j = 1;
                    foreach ($cdata['intersect'] as $intersect) {
              ?>
              <tr>
                <th scope="row"><?php echo $j; ?></th>
                <td><?php echo $intersect; ?></td>
              </tr>
              <?php
                    $j++;
                    }
                  }
              ?>
            </tbody>
          </table>
          <?php
            }
          ?>
        </div>
          
      </div>


  </div>
  <?php } else { ?>
    <div class="col-md-12 col-xs-12 col-lg-12">
      <div class="box-content card white">
      {{--         
        <p class="box-title">
          <a href="{{ route('crm.comparesheets') }}">
            <button type="button" class="btn btn-icon btn-icon-left btn-xs btn-violet waves-effect waves-light">
              <i class="ico fa fa-arrow-left"></i>
              Compare Sheets Again
            </button>
          </a>
        </p>
         --}}
        <h2 class="text-center" style="padding: 4% 1% 2% 1% ;">
          <span class="box-title ">
            <h4 class="css-no-data">NO DATA FOUND</h4>
          </span>
          <br>
          <a href="{{ route('crm.comparesheets') }}">
            <button type="button" class="btn btn-icon btn-icon-left btn-xs btn-violet waves-effect waves-light">
              <i class="ico fa fa-arrow-left"></i>
              Compare Sheets Again
            </button>
          </a>
        </h2>
      </div>
    </div>
  <?php } ?>
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
          ['Differences',  <?php echo count($cdata['diff']); ?>],
          ['Intersections',      <?php echo count($cdata['intersect']); ?>]
        ]);

        var options = {
          title: 'Sheets Statics',
          pieHole: 0.4,
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
      }
    </script>

