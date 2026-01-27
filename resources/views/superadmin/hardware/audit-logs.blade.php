@extends('superadmin.hardware.layout')

@section('title', __('pos.hardware_logs') . ' - ' . $business->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm mb-6">
        <a href="{{ route('superadmin.hardware.index') }}" class="text-blue-600 hover:text-blue-900">{{ __('pos.hardware_management') }}</a>
        <span class="text-gray-500">/</span>
        <a href="{{ route('superadmin.hardware.show', $business) }}" class="text-blue-600 hover:text-blue-900">{{ $business->name }}</a>
        <span class="text-gray-500">/</span>
        <span class="text-gray-900">{{ __('pos.hardware_logs') }}</span>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Device Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('pos.device_type') ?? 'Device Type' }}</label>
                <select name="device_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">{{ __('messages.all') ?? 'All' }}</option>
                    <option value="barcode_scanner" @if(request('device_type') === 'barcode_scanner') selected @endif>{{ __('pos.barcode_scanner') }}</option>
                    <option value="thermal_printer" @if(request('device_type') === 'thermal_printer') selected @endif>{{ __('pos.thermal_printer') }}</option>
                    <option value="cash_drawer" @if(request('device_type') === 'cash_drawer') selected @endif>{{ __('pos.cash_drawer') }}</option>
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.action') ?? 'Action' }}</label>
                <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">{{ __('messages.all') ?? 'All' }}</option>
                    <option value="scan" @if(request('action') === 'scan') selected @endif>Scan</option>
                    <option value="print" @if(request('action') === 'print') selected @endif>Print</option>
                    <option value="open_drawer" @if(request('action') === 'open_drawer') selected @endif>Open Drawer</option>
                    <option value="connect" @if(request('action') === 'connect') selected @endif>Connect</option>
                    <option value="disconnect" @if(request('action') === 'disconnect') selected @endif>Disconnect</option>
                    <option value="error" @if(request('action') === 'error') selected @endif>Error</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.status') ?? 'Status' }}</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">{{ __('messages.all') ?? 'All' }}</option>
                    <option value="success" @if(request('status') === 'success') selected @endif>{{ __('messages.success') ?? 'Success' }}</option>
                    <option value="failed" @if(request('status') === 'failed') selected @endif>{{ __('messages.failed') ?? 'Failed' }}</option>
                    <option value="pending" @if(request('status') === 'pending') selected @endif>{{ __('messages.pending') ?? 'Pending' }}</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    {{ __('messages.filter') ?? 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.timestamp') ?? 'Timestamp' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.user') ?? 'User' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('pos.device_type') ?? 'Device Type' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.device') ?? 'Device' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.action') ?? 'Action' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.status') ?? 'Status' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ __('messages.details') ?? 'Details' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ $log->logged_at->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="font-medium text-gray-900">{{ $log->user?->name ?? 'System' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                                    {{ $log->device_type === 'barcode_scanner' ? 'bg-blue-100 text-blue-800' :
                                       ($log->device_type === 'thermal_printer' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800') }}">
                                    @if($log->device_type === 'barcode_scanner')
                                        {{ __('pos.barcode_scanner') }}
                                    @elseif($log->device_type === 'thermal_printer')
                                        {{ __('pos.thermal_printer') }}
                                    @else
                                        {{ __('pos.cash_drawer') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->hardwareDevice?->device_name ?? __('messages.unknown') ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $log->getActionLabel() }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $log->status === 'success' ? 'bg-green-100 text-green-800' :
                                       ($log->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($log->details)
                                    <button onclick="showDetails('{{ htmlspecialchars($log->details) }}')" 
                                            class="text-blue-600 hover:text-blue-900 font-medium">
                                        {{ __('messages.view') ?? 'View' }}
                                    </button>
                                @elseif($log->error_message)
                                    <span class="text-red-600 text-xs">{{ Str::limit($log->error_message, 50) }}</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                {{ __('messages.no_logs_found') ?? 'No audit logs found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 mb-2">{{ __('messages.total_actions') ?? 'Total Actions' }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $logs->total() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 mb-2">{{ __('messages.success_rate') ?? 'Success Rate' }}</p>
            <p class="text-2xl font-bold text-green-600">
                @php
                    $successCount = App\Models\HardwareAuditLog::where('business_id', $business->id)->where('status', 'success')->count();
                    $successRate = $logs->total() > 0 ? round(($successCount / $logs->total()) * 100) : 0;
                @endphp
                {{ $successRate }}%
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 mb-2">{{ __('messages.failed_actions') ?? 'Failed Actions' }}</p>
            <p class="text-2xl font-bold text-red-600">
                @php
                    $failedCount = App\Models\HardwareAuditLog::where('business_id', $business->id)->where('status', 'failed')->count();
                @endphp
                {{ $failedCount }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600 mb-2">{{ __('messages.last_24_hours') ?? 'Last 24 Hours' }}</p>
            <p class="text-2xl font-bold text-blue-600">
                @php
                    $last24 = App\Models\HardwareAuditLog::where('business_id', $business->id)->where('logged_at', '>=', now()->subHours(24))->count();
                @endphp
                {{ $last24 }}
            </p>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('messages.details') ?? 'Details' }}</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
        </div>
        <div class="p-6">
            <pre id="detailsContent" class="bg-gray-50 p-4 rounded font-mono text-sm overflow-auto max-h-96"></pre>
        </div>
        <div class="p-6 border-t border-gray-200 text-right">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                {{ __('pos.cancel') }}
            </button>
        </div>
    </div>
</div>

<script>
    function showDetails(details) {
        document.getElementById('detailsContent').textContent = details;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    document.getElementById('detailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection
