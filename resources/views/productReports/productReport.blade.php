@extends('layouts.app')

@section('title', "Product Reports")

@section('content')
    @inject('permission', 'App\Http\Controllers\API\PermissionController')
    <product-reports>
    </product-reports>
@endsection
<!-- copy of src/resources/views/productReports/ReportsIndex.blade.php -->