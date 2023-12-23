<div class="card card-profile bg-secondary shadow">
    <div class="card-header">

        <div class="row align-items-center">
            <div class="col-8">
                <h3 class="mb-0">{{ __("Working Hours")}}</h3>
            </div>
            <div class="col-4 text-right">
                <a href="{{ route('admin.restaurant.addshift',[$restorant->id]) }}" class="btn btn-sm btn-primary">{{__('Add new shift')}}</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if (count($shifts)>1)
        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                <?php $index=0; ?>
                @foreach ($shifts  as $shiftId => $hours)
                    <?php $index++; ?>
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 {{  $index==1?"active":"" }}" id="tabs-shift-{{$shiftId}}-tab" data-toggle="tab" href="#shift{{$shiftId}}" role="tab" aria-controls="tabs-shift-{{$shiftId}}" aria-selected="true">{{ __('Shift')." ". $index }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="tab-content" id="shifts">
            <?php $index=0; ?>
        @foreach ($shifts  as $shiftId => $hours)
            <?php $index++; ?>
            <div class="tab-pane fade show {{  $index==1?"active":"" }}" id="shift{{$shiftId}}" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                <form method="post" action="{{ route('restaurant.workinghours') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="rid" name="rid" value="{{ $restorant->id }}"/>
                    <input type="hidden" id="shift_id" name="shift_id" value="{{ $shiftId }}"/>
                    <div class="form-group">
                        @foreach($days as $key => $value)
                        <br/>
                        <div class="row">
                            <div class="col-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="days" class="custom-control-input" id="{{ 'day'.$key.'_shift'.$shiftId }}" value={{ $key }} valuetwo={{ $shiftId }}>
                                    <label class="custom-control-label" for="{{ 'day'.$key.'_shift'.$shiftId }}">{{ __($value) }}</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-time-alarm"></i></span>
                                    </div>
                                    <input id="{{ $key.'_from'.'_shift'.$shiftId }}" name="{{ $key.'_from'.'_shift'.$shiftId }}" class="flatpickr datetimepicker form-control" type="text" placeholder="{{ __('Time') }}">
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <p class="display-4">-</p>
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-time-alarm"></i></span>
                                    </div>
                                    <input id="{{ $key.'_to'.'_shift'.$shiftId }}" name="{{ $key.'_to'.'_shift'.$shiftId }}" class="flatpickr datetimepicker form-control" type="text" placeholder="{{ __('Time') }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center">
                        @if(count($shifts)>1)
                            <a class="btn btn-danger mt-4" href="{{ route('restaurant.workinghoursremove',$shiftId) }}" style="color: #fff">{{ __('Delete') }}</a>
                        @endif
                        <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>   
                    </div>
                </form>
            </div>
        @endforeach
        </div>
        
    </div>
</div>
