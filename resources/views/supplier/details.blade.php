@extends('layouts.app')

@section('title', '| Supplier details')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Supplier details',
        'menu' => [
            '/suppliers' => ['icon' => 'fa-truck', 'name' => 'Suppliers'],
            '' => ['last' => true, 'name' => 'Supplier details']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            <table class="table table-bordered" style="width: 60%;">
                <tr>
                    <td colspan="2" width="85%" align="center">
                        <strong style="font-size: 20px;">{{ $supplier->name }}</strong><br>
                        <strong>
                            Official phone: {{ phone_format($supplier->phone_number) }}<br>
                            @if($supplier->fax_number)Fax: {{ phone_format($supplier->fax_number) }}@endif
                        </strong>
                    </td>
                    <td width="15%" rowspan="{{ 7 + (!$supplier->contactPersons->isEmpty() ? !$supplier->contactPersons->count() + 1 : 0)  }}" style="background-color: #ffffff; text-align: center;">
                        <img @if($supplier->logo)src="/uploads/suppliers/supplier-{{ $supplier->id }}/{{ $supplier->logo }}"@else src="/assets/img/404-logo.png"@endif style="max-height: 200px; max-width: 200px;">
                    </td>
                </tr>
                @if(!$supplier->contactPersons->isEmpty())
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="2"><strong>Contact persons</strong></td>
                    </tr>
                    @foreach($supplier->contactPersons as $person)
                        <tr>
                            <td>{{ $person->full_name }}</td>
                            <td>
                                Phone: {{ phone_format($person->phone_number) }}<br>
                                Email: <a href="mailto:{{ $person->email }}">{{ $person->email }}</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr style="background-color: #f9f9f9;">
                    <td colspan="2"><strong>Other information</strong></td>
                </tr>
                <tr>
                    <td>Country</td>
                    <td>{{ Countries::getOne($supplier->country) }}</td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td>{{ $supplier->address }}</td>
                </tr>
                <tr>
                    <td>Web address</td>
                    <td><a href="{{ $supplier->web_address }}" target="_blank">{{ $supplier->web_address }}</a></td>
                </tr>
                <tr>
                    <td>Active</td>
                    <td>@include('layouts.html.active-glyphicon', ['active' => $supplier->active]){{ $supplier->active ? 'Yes' : 'No' }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection