@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $product->name }}</h1>
    <div class="row">
        <div class="col-md-6">

            <img src="{{ asset($product->images[0]) }}" alt="{{ $product->name }}" class="img-fluid mb-3">
        </div>
        <div class="col-md-6">
            <h2>مشخصات فنی:</h2>
            {!! nl2br(e($product->technical_specs)) !!}
            <hr>







            <h2>Manufacturer: {{ $product->manufacturer }}</h2>
            <h2>Part Number: {{ $product->part_number }}</h2>
            <h2>Brand: {{ $product->brand }}</h2>
            <h2>Price: {{ number_format($product->price) }} تومان</h2>
            <p class="card-text"> موجودی: @if ($product->stock > 0) در انبار @else ناموجود @endif </p>
            <p class="card-text"> ضمانت: {{ $product->warranty }}</p>
            <hr>
            <h2>قطعه‌ای سازگار:</h2>
            <ul>
                @foreach ($compatibleModels as $model)
                    <li>{{ $model->name }}</li>
                @endforeach
            </ul>
            <hr>
            <button class="btn btn-primary" onclick="addToCart({{ $product->id }})">افزودن به سبد خرید</button>
        </div>
    </div>
</div>
@endsection
