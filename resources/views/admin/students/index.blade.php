@extends('layouts.admin') @section('content')
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Administration</p>
                <h1 class="mt-1 text-3xl font-black text-blue-950">Students</h1>
            </div>
            <form class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Name, email or matric no."
                    class="min-w-64 border bg-white px-3 py-2"><select name="status" class="border bg-white px-3 py-2">
                    <option value="">All KYC states</option>
                    @foreach (['pending', 'verified', 'rejected'] as $s)
                        <option @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                <button class="bg-blue-950 px-4 py-2 font-bold text-white">Filter</button>
            </form>
        </div>
        <div class="mt-8 overflow-x-auto border bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500">
                    <tr>
                        <th class="p-3">Student</th>
                        <th class="p-3">Matric</th>
                        <th class="p-3">KYC</th>
                        <th class="p-3">Account</th>
                        <th class="p-3">Activity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($students as $s)
                        <tr>
                            <td class="p-3"><strong>{{ $s->name }}</strong>
                                <div class="text-slate-500">{{ $s->email }}</div>
                            </td>
                            <td class="p-3">{{ $s->matric_no }}</td>
                            <td class="p-3 font-bold uppercase">
                                {{ $s->verification?->verification_status ?? 'not submitted' }}</td>
                            <td class="p-3">{{ $s->account_status }}</td>
                            <td class="p-3">{{ $s->listings_count }} listings · {{ $s->purchases_count }} purchases ·
                                {{ $s->sales_count }} sales</td>
                            <td class="p-3"><a class="font-bold text-blue-800"
                                    href="{{ route('admin.students.show', $s) }}">Review</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $students->links() }}</div>
    </div>
@endsection
