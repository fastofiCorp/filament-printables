<?php

namespace FastofiCorp\FilamentPrintables\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use FastofiCorp\FilamentPrintables\Models\FilamentPrintable;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Ticketpark\HtmlPhpExcel\HtmlPhpExcel;

class PrintInput extends Action
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

    protected function handle(Model $record, array $data)
    {
        if (! isset($data['printable'])) {
            Notification::make('')->danger()
                ->title(__('filament-printables::filament-printables.resource.notifications.no-template.title'))
                ->body(__('filament-printables::filament-printables.resource.notifications.no-template.description'))
                ->send();
        } else {
            $printable = FilamentPrintable::find($data['printable']);
            if ($printable) {
                switch ($data['format']) {
                    case 'pdf':

                        return response()->streamDownload(function () use ($printable, $record) {
                            echo Pdf::loadHtml(
                                Blade::render($printable->template_view, ['record' => $record], deleteCachedView: true)
                            )->stream();
                        }, $printable->slug.'-'.$record->id.'.pdf');

                    case 'xlsx':

                        return response()->streamDownload(function () use ($printable, $record) {

                            $htmlPhpExcel = new HtmlPhpExcel(Blade::render($printable->template_view, ['record' => $record], deleteCachedView: true));
                            echo $htmlPhpExcel->process()->output();
                        }, $printable->slug.'-'.$record->id.'.xlsx');
                }
            }
        }
    }

    public function getFormSchema(): array
    {

        $printables = FilamentPrintable::where('type', 'form')->whereJsonContains('linked_resources', $this->getModel())->get();
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
                                return $options[$format] = __('filament-printables::filament-printables.resource.fields.format.options.'.$format);
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
