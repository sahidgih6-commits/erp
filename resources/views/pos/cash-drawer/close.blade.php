@extends('layouts.app')

@section('title', 'Close Cash Drawer')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">ক্যাশ ড্রয়ার সেশন বন্ধ করুন</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">সেশনের তথ্য</h2>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-gray-600 text-sm">ক্যাশিয়ার</p>
                <p class="font-semibold">{{ $session->user->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">শুরুর সময়</p>
                <p class="font-semibold">{{ $session->opened_at->format('d M Y H:i A') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">শুরুর ব্যালেন্স</p>
                <p class="font-semibold text-blue-600">৳{{ number_format($session->opening_balance, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">মোট বিক্রয়</p>
                <p class="font-semibold text-green-600">৳{{ number_format($session->total_sales, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">আশা করা ব্যালেন্স</p>
                <p class="font-semibold text-purple-600">৳{{ number_format($session->expected_balance, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('pos.cash-drawer.update', $session) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="closing_balance" class="block text-gray-700 text-sm font-bold mb-2">শেষ ব্যালেন্স (৳) *</label>
                <input type="number" step="0.01" name="closing_balance" id="closing_balance" value="{{ old('closing_balance', $session->expected_balance) }}" 
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 @error('closing_balance') border-red-500 @enderror" required min="0">
                @error('closing_balance')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-600 text-xs mt-1">ক্যাশ ড্রয়ারে বর্তমানে কত টাকা আছে তা গণনা করে লিখুন</p>
            </div>

            <div id="differenceAlert" class="hidden mb-4 p-4 rounded"></div>

            <div class="mb-4">
                <label for="closing_notes" class="block text-gray-700 text-sm font-bold mb-2">ক্লোজিং নোট</label>
                <textarea name="closing_notes" id="closing_notes" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700">{{ old('closing_notes') }}</textarea>
                <p class="text-gray-600 text-xs mt-1">যদি কোনো অমিল থাকে তাহলে ব্যাখ্যা দিন</p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                <p class="text-yellow-900 font-semibold">মনে রাখবেন:</p>
                <ul class="list-disc list-inside text-yellow-800 text-sm mt-2">
                    <li>সাবধানে ক্যাশ ড্রয়ারে টাকা গণনা করুন</li>
                    <li>অমিল থাকলে ব্যাখ্যা দিন</li>
                    <li>সেশন বন্ধ হয়ে গেলে পুনরায় খোলা যাবে না</li>
                </ul>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('pos.cash-drawer.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    সেশন বন্ধ করুন
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('closing_balance').addEventListener('input', function() {
    const expected = {{ $session->expected_balance }};
    const actual = parseFloat(this.value) || 0;
    const difference = actual - expected;
    
    const alertDiv = document.getElementById('differenceAlert');
    if (Math.abs(difference) > 0.01) {
        alertDiv.classList.remove('hidden');
        if (difference > 0) {
            alertDiv.className = 'mb-4 p-4 rounded bg-green-100 border-l-4 border-green-500';
            alertDiv.innerHTML = `<p class="text-green-900 font-semibold">অতিরিক্ত: ৳${difference.toFixed(2)}</p>`;
        } else {
            alertDiv.className = 'mb-4 p-4 rounded bg-red-100 border-l-4 border-red-500';
            alertDiv.innerHTML = `<p class="text-red-900 font-semibold">কম: ৳${Math.abs(difference).toFixed(2)}</p>`;
        }
    } else {
        alertDiv.classList.add('hidden');
    }
});
</script>
@endsection
