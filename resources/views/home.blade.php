@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Upload Image') }}</div>
                @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
                @endif
                <div class="card-body">
                    <form method="post" action="{{ route('upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputEmail1">Image</label>
                            <input type="file" class="form-control" name="file" id="file" aria-describedby="emailHelp" placeholder="Enter File">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection