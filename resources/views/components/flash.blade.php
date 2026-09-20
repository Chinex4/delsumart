@if (session('success'))
    <x-alert tone="success">
        {{ session('success') }}</x-alert>
@endif
@if (session('warning'))
    <x-alert tone="warning">{{ session('warning') }}</x-alert>
@endif
@if ($errors->any())
    <x-alert tone="danger" title="Please check the following">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
