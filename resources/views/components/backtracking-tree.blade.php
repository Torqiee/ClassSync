@props(['nodes', 'edges'])

<div class="w-full overflow-x-auto">
    <svg class="w-full h-auto" style="min-height: 600px;" viewBox="0 0 1200 700">
        <defs>
            <marker id="arrowhead" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto">
                <polygon points="0 0, 10 3, 0 6" fill="#64748B" />
            </marker>
            <marker id="arrowhead-error" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto">
                <polygon points="0 0, 10 3, 0 6" fill="#EF4444" />
            </marker>
        </defs>

        {{-- Connection Lines --}}
        <g stroke="#64748B" stroke-width="2" stroke-dasharray="5,5" fill="none" marker-end="url(#arrowhead)">
            @foreach($edges as $edge)
                <!-- Connector from {{ $edge['from'] }} to {{ $edge['to'] }} -->
                <line x1="{{ $loop->index * 100 + 150 }}" y1="120" x2="{{ $loop->index * 100 + 150 }}" y2="200" />
            @endforeach
        </g>

        {{-- Nodes Rendering --}}
        @foreach($nodes as $node)
            @if($node['type'] === 'start')
                {{-- START Node --}}
                <g transform="translate(600, 80)">
                    <rect x="-180" y="-30" width="360" height="60" rx="30" fill="#6366F1" opacity="0.9" stroke="#818CF8" stroke-width="2"/>
                    <circle cx="-150" cy="0" r="6" fill="#22C55E"/>
                    <text x="-20" y="8" font-size="16" font-weight="bold" fill="white" font-family="system-ui">
                        {{ $node['label'] }}
                    </text>
                </g>
            @elseif($node['type'] === 'process')
                {{-- PROCESS Node --}}
                <g transform="translate(600, 260)">
                    <rect x="-280" y="-50" width="560" height="100" rx="8" fill="#374151" opacity="0.8" stroke="#4B5563" stroke-width="2"/>
                    <text x="-270" y="-25" font-size="12" font-weight="bold" fill="#9CA3AF" font-family="system-ui" letter-spacing="2">
                        {{ $node['label'] }}
                    </text>
                    <text x="-270" y="10" font-size="14" font-weight="600" fill="#FFFFFF" font-family="system-ui">
                        {{ Str::limit($node['description'] ?? '', 60) }}
                    </text>
                </g>
            @elseif($node['type'] === 'error')
                {{-- ERROR Node --}}
                <g transform="translate(600, 520)">
                    <rect x="-320" y="-35" width="640" height="70" rx="6" fill="#475569" opacity="0.7" stroke="#64748B" stroke-width="1" stroke-dasharray="3,3"/>
                    <text x="-310" y="10" font-size="13" font-weight="600" fill="#94A3B8" font-family="system-ui" font-style="italic">
                        {{ $node['description'] ?? $node['label'] }}
                    </text>
                </g>
            @endif
        @endforeach
    </svg>
</div>
