<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Facades\Rule;

final class UserTable extends PowerGridComponent
{
	public string $tableName = 'user-table-ix1nd5-table';

	public function setUp(): array
	{
		$this->showCheckBox();

		return [
			PowerGrid::header()
				->showSearchInput()
				->showToggleColumns(),
			PowerGrid::lazy()
				->rowsPerChildren(25),
			PowerGrid::footer()
				->showPerPage()
				->showRecordCount(),
			PowerGrid::detail()
				->view('components.detail')
				->showCollapseIcon(),
		];
	}

	public function datasource(): Builder
	{
		return User::query();
	}

	public function relationSearch(): array
	{
		return [];
	}

	public function fields(): PowerGridFields
	{
		return PowerGrid::fields()
			->add('id')
			->add('name')

			/** Example of custom column using a closure **/
			->add('name_lower', fn(User $model) => strtolower(e($model->name)))

			->add('email')
			->add('created_at_formatted', fn(User $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
	}

	public function columns(): array
	{
		return [
			Column::make('Id', 'id'),
			Column::make('Name', 'name')
				->sortable()
				->searchable(),

			Column::make('Email', 'email')
				->sortable()
				->searchable(),

			Column::make('Created at', 'created_at_formatted', 'created_at')
				->sortable(),

			Column::action('Action')
		];
	}

	public function filters(): array
	{
		return [
			Filter::inputText('name')->operators(['contains']),
			Filter::inputText('email')->operators(['contains']),
			Filter::datetimepicker('created_at'),
		];
	}

	#[\Livewire\Attributes\On('edit')]
	public function edit($rowId): void
	{
		$this->js('alert(' . $rowId . ')');
	}

	public function actions($row): array
	{
		return [
			Button::add('detail')
				->slot('Detail')
				->class('bg-blue-500 text-white font-bold py-2 px-2 rounded')
				->toggleDetail($row->id),
		];
	}

	public function actionRules(): array
	{
		return [
			Rule::rows()
				->when(fn($user) => $user->id == 1)
				->detailView('components.detail-rules', ['test' => 1]),
		];
	}


	/*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
