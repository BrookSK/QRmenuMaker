<br />
<div class="card card-profile shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-8">
                <h5 class="h3 mb-0">{{ __('Allergens') }}</h5>
            </div>
            <div class="col-4 text-right">
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="table-responsive">
        <form method="post" action="{{ route('allergens.set', $item) }}" autocomplete="off" enctype="multipart/form-data">
            @csrf
        <table class="table align-items-center">
            <thead class="thead-light">
                <tr>
                    <th scope="col" class="sort" data-sort="imagr">{{ __('Image') }}</th>
                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                    <th scope="col">{{ __('Contains') }}</th>
                </tr>
            </thead>
            <tbody class="list">
                    <?php $selected=$item->allergens()->pluck('posts.id')->toArray(); ?>
                    @foreach($allergens as $alergen)
                        <tr>
                            <th scope="row"><img style="height:30px" src="{{ $alergen->image_link }}" /></th>
                            <th scope="row">{{ $alergen->title }}</th>
                            <th scope="row">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class=""  @if (in_array($alergen->id,$selected))
                                        checked
                                    @endif name="allergens[{{  $alergen->id }}]">
                                </div>
                                
                            </th>

                        </tr>
                    @endforeach
                   
            
            </tbody>
        </table>
        <div class="text-center mb-2">
            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
        </div>
        </form>
    </div>
    <!-- end content -->
</div>