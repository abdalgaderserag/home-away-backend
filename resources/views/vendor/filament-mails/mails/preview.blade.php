@extends('vendor.filament-mails.mails.html')

@section('content')
<div class="prose prose-sm sm:prose lg:prose-lg xl:prose-2xl max-w-full overflow-x-auto">
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Mail Preview</h2>
        <div class="border-t pt-4">
            <div class="whitespace-pre-wrap break-words">
                {!! $html !!}
            </div>
        </div>
    </div>
</div>
@endsection
