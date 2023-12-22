@if(count($fieldsToRender)>0)
<div class="card card-profile shadow">
    <div class="px-4">
      <div class="mt-5">
        <h3>{{ __(config('settings.label_on_custom_fields')) }}<span class="font-weight-light"></span></h3>
      </div>
      <div class="card-content border-top">
        <br />
        @include('partials.fields',['fields'=>$fieldsToRender])
      </div>
      <br />
      <br />
    </div>
</div>
<br/>
@endif
