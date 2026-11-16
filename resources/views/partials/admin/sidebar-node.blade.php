{{--
    Satu node menu sidebar (rekursif, maks. 3 tingkat).
    $node: id, level, title, icon, url, active, children[] — disusun di partials/sidebar.blade.php.
--}}
@php
    $hasChildren = !empty($node['children']);
    $collapseId = $node['id'];
@endphp
<li class="{{ $node['level'] === 1 || $hasChildren ? 'pe-slide' : 'pe-slide-item' }}{{ $hasChildren ? ' pe-has-sub' : '' }} pe-menu-level-{{ $node['level'] }}{{ $node['active'] ? ' active' : '' }}"
    data-menu-node>
    @if ($hasChildren)
        <a href="#{{ $collapseId }}" class="pe-nav-link{{ $node['active'] ? '' : ' collapsed' }}" role="button"
            data-bs-toggle="collapse" aria-expanded="{{ $node['active'] ? 'true' : 'false' }}"
            aria-controls="{{ $collapseId }}">
            @if ($node['level'] === 1)
                <i class="{{ $node['icon'] ?: 'fas fa-circle' }} pe-nav-icon"></i>
            @endif
            <span class="pe-nav-content">{{ $node['title'] }}</span>
            <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
        </a>
        <ul class="pe-slide-menu collapse{{ $node['active'] ? ' show' : '' }}" id="{{ $collapseId }}"
            data-bs-parent="#{{ $parentId }}">
            @foreach ($node['children'] as $childNode)
                @include('partials.admin.sidebar-node', ['node' => $childNode, 'parentId' => $collapseId])
            @endforeach
        </ul>
    @else
        <a href="{{ $node['url'] }}" class="pe-nav-link{{ $node['active'] ? ' active' : '' }}"
            @if ($node['active']) aria-current="page" @endif>
            @if ($node['level'] === 1)
                <i class="{{ $node['icon'] ?: 'fas fa-circle' }} pe-nav-icon"></i>
                <span class="pe-nav-content">{{ $node['title'] }}</span>
            @else
                {{ $node['title'] }}
            @endif
        </a>
    @endif
</li>
