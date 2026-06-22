<x-layouts.app :title="__('portfolio.diagnostic.title') . ' | ' . __('portfolio.app_name')">
    <div
        id="diagnostic-root"
        data-payload='@json($diagnosticPayload)'
    ></div>
</x-layouts.app>
