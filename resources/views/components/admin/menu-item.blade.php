@props(['item'])

<li class="dd-item" data-id="{{ $item->id }}">
    <div class="dd-handle">
        <span class="dd-title">{{ $item->title }}</span>
        <div class="dd-nodrag">
             <span class="badge badge-light border mr-2">
                @if($item->linkable_type)
                    @if($item->linkable_id === null)
                         <i class="fas fa-magic text-warning"></i> Dynamic
                    @else
                        {{ class_basename($item->linkable_type) }}
                    @endif
                @else
                    Link
                @endif
            </span>
            <span class="btn-action-menu" wire:click="deleteItem({{ $item->id }})">
                <i class="fas fa-trash-alt"></i>
            </span>
        </div>
    </div>
    @if($item->children->count() > 0)
        <ol class="dd-list">
            @foreach($item->children as $child)
                <x-admin.menu-item :item="$child" />
            @endforeach
        </ol>
    @endif
</li>
