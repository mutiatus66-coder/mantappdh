```blade id="qq7tqk"
@extends('index', ['dummy' => true])

@section('content')

<div class="card">
    <div class="card-body">
        <h1>TES BIDANG</h1>

        <pre>
            {{ print_r($subEvents, true) }}
        </pre>
    </div>
</div>

@endsection
```
