@extends('crm.crmlayout')
@section('content')


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
        <h4 class="box-title">ADD Payout</h4><hr>
        <div class="dropdown js__drop_down">
            <a href="#" class="dropdown-icon glyphicon glyphicon-option-vertical js__drop_down_button"></a>
            <ul class="sub-menu">
                <li><a href="{{ route('crm-payout') }}">All Payouts</a></li>
                <li><a href="{{ route('payout') }}">Add Payout</a></li>
            </ul>
        </div>

        <?php /* ?>
        <div class="box-content">
            <div id="accordion" class="js__ui_accordion">
                <h3 class="accordion-title">Life Insurance</h3>
                <div class="accordion-content">
                    There are two major types of life insurance—term and whole life. 
                    Whole life is sometimes called permanent life insurance, and it encompasses several subcategories, including traditional whole life, universal life, variable life and variable universal life.
                </div>
                <h3 class="accordion-title">Motor Insurance</h3>
                <div class="accordion-content">
                    Motor Insurance deals with the insurance covers for the loss or damage caused to the automobile or its parts due to natural and man-made calamities.
                    It provides accident cover for individual owners of the vehicles while driving and also for passengers and third-party legal liability.
                </div>
                <h3 class="accordion-title">Health insurance</h3>
                <div class="accordion-content">
                    Health insurance encompasses two types - Indemnity plans and Definite Benefit Plan. The indemnity plans are traditional health covers which cover hospitalization costs from the sum assured. Definite benefit plans offer lump sum payment on detection of illness.
                </div>
                <h3 class="accordion-title">Travel insurance</h3>
                <div class="accordion-content">
                    <h3>What is travel insurance?</h3>
                    Domestic travel insurance, international travel insurance, medical travel insurance and senior citizen travel insurance are different types of travel insurance policies in India.
                    A travel insurance policy is ideal should you be contemplating travelling, either within the country or overseas. The purpose of your travel – for business or leisure – notwithstanding, purchasing travel insurance is the first step towards staying guarded against a range of risks and financial losses that could rear their ugly heads over the course of your trip. Based on your specific requirements, you can choose from a trove of travel insurance plans.
                    <ul>
                        <li>
                            <h3>DOMESTIC TRAVEL INSURANCE PLAN</h3>
                            <p>
                                This policy is designed for customers intending to travel within the contours of the country. A domestic travel insurance policy insulates the policyholder from expenses that may result from treatment of a medical emergency, theft/loss of baggage and other valuables, delays/cancellation of flights, permanent disability, and personal liability (refers to third-party damages inflicted by you while you’re on the trip).
                            </p>
                        </li>
                        <li>
                            <h3>INTERNATIONAL TRAVEL INSURANCE</h3>
                            <p>
                                This policy is designed in keeping with what customers – travelling internationally – would want. Besides the usual coverage offered by its domestic counterpart, an international travel insurance policy safeguards you (policyholder) against risks of a flight hijack, repatriation to India, etc.
                            </p>
                        </li>
                        <li>
                            <h3>MEDICAL TRAVEL INSURANCE</h3>
                            <p>
                                The name is the marker here – with the policy specifically designed to cover expenses emanating from medical emergencies and other healthcare-related concerns. However, the exact set of inclusions and exclusions will vary across insurance providers.
                            </p>
                        </li>
                        <li>
                            <h3>GROUP TRAVEL INSURANCE</h3>
                            <p>
                                Consider a group of employees travelling abroad to participate in a business conclave. In such a situation, it would not make sense for every individual in the group to purchase his/her own travel insurance policy – for the simple reason that it could compound premiums considerably
                                A group travel insurance policy is of much help here – considering it can help you save plenty on premiums, without having to compromise on the safety net against any unanticipated and adverse development that might take shape through the course of the trip.
                            </p>
                        </li>
                        <li>
                            <h3>SENIOR CITIZEN TRAVEL INSURANCE</h3>
                            <p>
                                Besides the usual advantages of purchasing travel insurance, a policy that is directed at senior citizens (generally belonging to the age group of 61-70 years) offers additional coverage against dental treatments/procedures as well as cashless hospitalization.
                            </p>
                        </li>
                        <li>
                            <h3>SINGLE AND MULTI-TRIP TRAVEL INSURANCE</h3>
                            <p>
                                As the name suggests, a single-trip travel insurance policy retains its validity through the time you are on a trip. It covers both medical as well as non-medical expenses (such as baggage loss, delays in flights, etc.).
                                Multi-trip travel insurance policy, on the other hand, provides extended coverage (lasting usually a year in most cases) so that frequent flyers don’t have to go through the entire process of availing insurance every time they prep for travel.
                                In conclusion, you should choose a type that best fits your requirements, only after having accounted for what you might need the most over the course of travel.
                            </p>
                        </li>
                        <li>List item</li>
                    </ul>
                </div>
                <h3 class="accordion-title">Property insurance</h3>
                <div class="accordion-content">
                    <p></p>
                    <ul>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                    </ul>
                </div>
                <h3 class="accordion-title">Mobile insurance</h3>
                <div class="accordion-content">
                    <p></p>
                    <ul>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                    </ul>
                </div>
                <h3 class="accordion-title">Cycle insurance</h3>
                <div class="accordion-content">
                    <p></p>
                    <ul>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                    </ul>
                </div>
                <h3 class="accordion-title">Bite-size insurance</h3>
                <div class="accordion-content">
                    <p></p>
                    <ul>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                        <li>List item</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php */ ?>



        <form id="payoutForm" action="{{ route('crm.store-payout') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <div class="title">
                    <input type="text" name="title" class="form-control" id="title" placeholder="Title">
                </div>
            </div>

            <div class="form-group margin-bottom-20">
                <label for="insurance">Insurance</label>
                <select class="form-control" name="insurance" id="insurance">
                    <option value="0">Insurance</option>
                    <option value="1">Type 1</option>
                    <option value="2">Type 2</option>
                    <option value="3">Type 3</option>
                    <option value="4">Type 4</option>
                    <option value="5">Type 5</option>
                    <option value="6">Type 6</option>
                    <option value="7">Type 7</option>
                    <option value="8">Type 8</option>
                </select>
            </div>
      
            <div class="form-group">
                <label for="payout_file">Payout File</label>
                <input type="file" id="payout_file" name="payout_file" class="dropify" data-default-file="http://placehold.it/1000x667" />
            </div>

            <hr>
            <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">Add</button>
        </form>
    </div>

</div>

</div>

@endsection

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$( function() {
    $( "#accordion" ).accordion();
} );
</script>