<div class="modal fade" id="modal-clone_vendor" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title-new-item">{{ __('Clone restaurant') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <form id="form-assing-driver" method="POST" action="{{ route('cloner.index')}}">
                            @csrf 
                            <p>{{ __('Create the new user that will be the owner of this restaurant.')}}</p>
                            @include('partials.input',['id'=>'name','name'=>'Name','placeholder'=>'Owner name','value'=>'', 'required'=>true])
                            @include('partials.input',['id'=>'email','name'=>'Email','placeholder'=>'Email','value'=>'', 'required'=>true])
                            @include('partials.input',['type'=>"password",'id'=>'password','name'=>'Password','placeholder'=>'Password','value'=>'', 'required'=>true])
                            @include('partials.input',['type'=>"hidden",'id'=>'id','name'=>'ID','placeholder'=>'ID','value'=>$id, 'required'=>true])
                            @include('partials.input',['id'=>'phone','name'=>'Owner Phone','placeholder'=>'Owner Phone','value'=>'', 'required'=>true])
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4">{{ __('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>