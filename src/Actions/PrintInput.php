<?php

namespace FastofiCorp\FilamentPrintables\Actions;

use Closure;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Ticketpark\HtmlPhpExcel\HtmlPhpExcel;
use Filament\Forms\Components\Actions\Action;
use FastofiCorp\FilamentPrintables\Models\FilamentPrintable;

class PrintInput extends Action
{

    protected string|Closure $format = '';

    protected int|Closure $printable = 0;

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

    protected function setUp(): void
    {
        $this->modalWidth = 'sm';
        $this->action($this->handle(...));
    }

    protected function handle(array $data)
    {
        if ($this->recordData != []) {
            if (is_callable($this->recordData)) {
                $record = call_user_func($this->recordData);
            } else {
                $record = $this->recordData;
            }
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
                                Blade::render($printable->template_view, $this->recordData, deleteCachedView: true)
                            )->stream();
                        }, $printable->slug . '.pdf');

                    case 'xlsx':

                        return response()->streamDownload(function () use ($printable, $record) {

                            $htmlPhpExcel = new HtmlPhpExcel(Blade::render($printable->template_view, $this->recordData, deleteCachedView: true));
                            echo $htmlPhpExcel->process()->output();
                        }, $printable->slug .  '.xlsx');
                }
            }
        }
    }

    public function getFormSchema(): array
    {
        //Get the printables linked to the resource
        $printables = FilamentPrintable::where('type', 'form')->whereType('tag')->get();

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
