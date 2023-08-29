<?php

namespace FastofiCorp\FilamentPrintables\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use FastofiCorp\FilamentPrintables\Models\FilamentPrintable;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Ticketpark\HtmlPhpExcel\HtmlPhpExcel;

class PrintAction extends Action
{
    protected string|Closure $format = '';

    protected int|Closure $printable = 0;

    protected string $model = '';

    protected string|array|Closure $recordData = [];

    protected string|Closure|null $icon = 'heroicon-o-printer';

    public static function make(?string $name = 'print'): static
    {
        return parent::make($name);
    }

    protected function setLabel()
    {
        $this->label = __('filament-printables::filament-printables.resource.actions.print');
    }

    protected function model(string $model)
    {
        $this->model = $model;
    }

    protected function setUp(): void
    {
        $this->modalWidth = 'sm';
        $this->action($this->handle(...));
    }

    public function format(string|Closure $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function printable(int|Closure $printable): static
    {
        $this->printable = $printable;

        return $this;
    }

    public function data(string|array|Closure $recordData): static
    {
        $this->recordData = $recordData;

        return $this;
    }

    protected function handle(Model $record, array $data)
    {
        if ($this->recordData != []) {
            $record = $this->recordData;
        }

        if (isset($data['printable'])) {
            $this->printable = $data['printable'];
        }
        if (isset($data['format'])) {
            $this->format = $data['format'];
        }

        if ($this->printable == 0) {
            Notification::make('')->danger()
                ->title(__('filament-printables::filament-printables.resource.notifications.no-template.title'))
                ->body(__('filament-printables::filament-printables.resource.notifications.no-template.description'))
                ->send();
        } else {

            $printable = FilamentPrintable::find($this->printable);
            if ($printable) {

                switch ($this->format) {
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
        $model = $this->model != '' ? $this->model : $this->getModel();
        //Get the printables linked to the resource
        $printables = FilamentPrintable::where('type', 'form')->whereJsonContains('linked_resources', $model)->get();

        if ($this->printable != 0 and $this->format != '') {
            return [];
        }

        if ($printables->count() > 0) {
            return [
                Select::make('printable')
                    ->label(__('filament-printables::filament-printables.resource.fields.template.label'))
                    ->options($printables->pluck('name', 'id')->toArray())
                    ->default($this->printable != 0 ? $this->printable : null)
                    ->disabled($this->printable != 0)
                    ->required()
                    ->reactive()
                    ->autofocus()
                    ->placeholder(__('filament-printables::filament-printables.resource.fields.template.placeholder')),

                Select::make('format')
                    ->label(__('filament-printables::filament-printables.resource.fields.format.label'))
                    ->required()
                    ->default($this->format != '' ? $this->format : null)
                    ->disabled($this->format != '')
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
