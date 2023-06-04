<?php

namespace FastofiCorp\FilamentPrintables;

use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use FastofiCorp\FilamentPrintables\Models\FilamentPrintable;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Blade;
use Ticketpark\HtmlPhpExcel\HtmlPhpExcel;

class BulkPrintAction extends BulkAction
{
    protected string|Closure|null $icon = 'heroicon-o-printer';

    public static function make(?string $name = 'print'): static
    {
        return parent::make($name);
    }

    protected function setLabel()
    {
        $this->label = __('filament-printables::filament-printables.resource.actions.print');
    }

    protected function setUp(): void
    {
        $this->modalWidth = 'sm';
        $this->action($this->handle(...));
    }

    protected function handle(Collection $records, array $data)
    {
        if (!isset($data['printable'])) {
            Notification::make('')->danger()
                ->title(__('filament-printables::filament-printables.resource.notifications.no-template.title'))
                ->body(__('filament-printables::filament-printables.resource.notifications.no-template.description'))
                ->send();
        } else {
            $printable = FilamentPrintable::find($data['printable']);
            if ($printable) {
                switch ($data['format']) {
                    case 'pdf':

                        return response()->streamDownload(function () use ($printable, $records) {
                            echo Pdf::loadHtml(
                                Blade::render($printable?->template_view, ['records' => $records], deleteCachedView: true)
                            )->stream();
                        }, $printable?->slug . '-' . date('Y-m-d H:i:s') . '.pdf');



                    case 'xlsx':

                        return response()->streamDownload(function () use ($printable, $records) {
                            $htmlPhpExcel = new HtmlPhpExcel(Blade::render($printable?->template_view, ['records' => $records], deleteCachedView: true));
                            echo $htmlPhpExcel?->process()->output();
                        }, $printable?->slug . '-' . date('Y-m-d H:i:s') . '.xlsx');

                     
                }
            }
        }
    }

    public function getFormSchema(): array
    {

        $printables = FilamentPrintable::where('type', 'report')->whereJsonContains('linked_resources', $this->getModel())->get();
        if ($printables->count() > 0) {
            return [
                Select::make('printable')
                    ->label(__('filament-printables::filament-printables.resource.fields.template.label'))
                    ->options($printables->pluck('name', 'id')->toArray())
                    ->required()
                    ->reactive()
                    ->autofocus()
                    ->placeholder(__('filament-printables::filament-printables.resource.fields.template.placeholder')),

                Select::make('format')
                    ->label(__('filament-printables::filament-printables.resource.fields.format.label'))
                    ->required()
                    ->options(function ($get) {
                        $options = [];
                        if ($get('printable') != '') {
                            collect(FilamentPrintable::find($get('printable'))?->format)->map(function ($format) use (&$options) {
                                return $options[$format] = __('filament-printables::filament-printables.resource.fields.format.options.' . $format);
                            });
                        }

                        return $options;
                    }),

            ];
        } else {
            return [];
        }
    }
}
