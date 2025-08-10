<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    "data",
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    "data",
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

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
        Showing <?php echo e($data->firstItem()); ?> to <?php echo e($data->lastItem()); ?> of
        <?php echo e($data->total()); ?> entries
    </div>
    <div>
        <?php echo e($data->appends(request()->query())->links("pagination::bootstrap-4")); ?>

    </div>
</div>
<?php /**PATH E:\laragon\www\ta_intrackapp\resources\views/components/table-pagination.blade.php ENDPATH**/ ?>