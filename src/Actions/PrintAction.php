<?php

namespace FastofiCorp\FilamentPrintables\Actions;

use Closure;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Spatie\Browsershot\Browsershot;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use FastofiCorp\FilamentPrintables\Models\FilamentPrintable;

class PrintAction extends Action
{
    protected string|Closure|null $icon = "heroicon-o-printer";

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
        if (!isset($data['printable'])) {
            Notification::make('')->danger()
                ->title(__('filament-printables::filament-printables.resource.notifications.no-template.title'))
                ->body(__('filament-printables::filament-printables.resource.notifications.no-template.description'))
                ->send();
        } else {
            $printable = FilamentPrintable::find($data['printable']);

            return response()->streamDownload(function () use ($printable, $record) {
                echo Pdf::loadHtml(
                    Blade::render($printable->template_view, ['record' => $record], deleteCachedView: true)
                )->stream();
            }, $printable->slug . '-' . $record->id . '.pdf');
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
                                return $options[$format] = __('filament-printables::filament-printables.resource.fields.format.options.' . $format);
                            });
                        }

                        return $options;
                    })

            ];
        } else {
            return [];
        }
    }
}
