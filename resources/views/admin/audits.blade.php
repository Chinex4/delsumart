@extends('layouts.admin') @section('content')
    <div class="mx-auto max-w-6xl px-4 py-12">
        <h1 class="text-3xl font-black text-blue-950">Administrative audit log</h1>
        <div class="mt-8 overflow-x-auto border bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="p-4">Time</th>
                        <th class="p-4">Admin</th>
                        <th class="p-4">Action</th>
                        <th class="p-4">Target</th>
                        <th class="p-4">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr class="border-t">
                            <td class="p-4 whitespace-nowrap">{{ $log->created_at }}</td>
                            <td class="p-4">{{ $log->admin?->name ?? 'System' }}</td>
                            <td class="p-4 font-semibold">{{ $log->action_type }}</td>
                            <td class="p-4">{{ $log->target_type }} #{{ $log->target_id }}</td>
                            <td class="p-4 text-slate-600">{{ $log->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-8">{{ $logs->links() }}</div>
    </div>
@endsection
