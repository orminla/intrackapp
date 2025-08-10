@props([
    "data",
])

<style>
    .pagination {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        max-width: fit-content;
        border-radius: 4px;
        padding: 0;
    }
    .pagination svg {
        width: 16px;
        height: 16px;
    }
    .pagination .page-link {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        line-height: 1.3;
        padding-left: 1.2rem;
        padding-right: 1.2rem;
    }
    .pagination .page-item.active .page-link,
    .pagination .page-item.disabled .page-link {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
    .showing-text {
        color: #adb5bd;
        font-size: 0.85rem;
        margin: 0;
    }
</style>

<div
    class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2"
>
    <div class="showing-text text-muted">
        Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of
        {{ $data->total() }} entries
    </div>
    <div>
        {{ $data->appends(request()->query())->links("pagination::bootstrap-4") }}
    </div>
</div>
