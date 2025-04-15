@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('payment::payment_methods.payment_methods'))

    <li class="active">{{ trans('payment::payment_methods.payment_methods') }}</li>
@endcomponent

@component('admin::components.page.index_table')
    @slot('tabs', ['magnati' => $tabs])
    @slot('buttons', ['create'])
@endcomponent
