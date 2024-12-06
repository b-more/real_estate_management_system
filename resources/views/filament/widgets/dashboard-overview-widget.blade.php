<x-filament::widget>
    <x-filament::card>
        <div id="dashboard-overview" data-stats="{{ json_encode($this->getViewData()['widgetData']) }}"></div>
    </x-filament::card>
</x-filament::widget>