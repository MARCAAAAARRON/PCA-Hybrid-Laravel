<div class="space-y-6">
    @php
        $record = $getRecord();
        $modelName = get_class($record);
        $logs = \App\Models\AuditLog::with('user')
            ->where('model_name', $modelName)
            ->where('object_id', $record->id)
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp

    @if($logs->isEmpty())
        <div class="text-sm text-gray-500 italic">No audit history available for this record.</div>
    @else
        <div class="relative border-l border-gray-200 dark:border-gray-700 ml-3">
            @foreach($logs as $index => $log)
                <div class="mb-6 ml-6 {{ $loop->last ? '' : '' }}">
                    @php
                        $color = match($log->action) {
                            'create' => 'bg-green-500',
                            'update' => 'bg-blue-500',
                            'submit' => 'bg-purple-500',
                            'validate' => 'bg-emerald-500',
                            'revision' => 'bg-orange-500',
                            'delete' => 'bg-red-500',
                            default => 'bg-gray-500'
                        };
                        
                        $icon = match($log->action) {
                            'create' => 'M12 4v16m8-8H4', // plus
                            'update' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', // pencil
                            'submit' => 'M5 13l4 4L19 7', // check (basic)
                            'validate' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', // shield check
                            'revision' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', // x-circle
                            'delete' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', // trash
                            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' // info
                        };
                    @endphp

                    <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-4 ring-white dark:ring-gray-900 {{ $color }}">
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                        </svg>
                    </span>
                    
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="flex items-center text-sm font-semibold text-gray-900 dark:text-white">
                            {{ ucfirst($log->action) }} 
                            @if($index === 0)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ml-3">Latest</span>
                            @endif
                        </h3>
                        <time class="block text-xs font-normal leading-none text-gray-400 dark:text-gray-500">
                            {{ $log->created_at->format('M d, Y • h:i A') }}
                        </time>
                    </div>
                    
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        By <span class="font-medium text-gray-700 dark:text-gray-300">{{ $log->user?->name ?? 'System' }}</span>
                    </p>
                    
                    @if(!empty($log->details))
                        <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs font-mono text-gray-600 dark:text-gray-400 shadow-inner">
                            {{ $log->formatted_details }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
